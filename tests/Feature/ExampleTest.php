<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\CapstoneStages;
use App\Models\Group;
use App\Models\Teacher;
use App\Models\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('WELCOME TO CAPSTONE TRACKER');
        $response->assertSee('Please input Your School ID');
    }

    public function test_school_id_confirmation_uses_the_registered_route(): void
    {
        User::create([
            'user_id' => '2023-1234',
            'role' => 'student',
        ]);

        $this->get('/')
            ->assertStatus(200)
            ->assertSee('action="' . route('id.check') . '"', false);

        $this->post(route('id.check'), ['id' => '2023-1234'])
            ->assertRedirect('/');

        $this->assertSame('2023-1234', session('user_id'));
    }

    /**
     * Test home page when user has already registered (password is not null).
     * It should show only the Sign In form.
     */
    public function test_homepage_shows_sign_in_form_only_when_user_has_account(): void
    {
        // Create user with a password
        $user = User::create([
            'user_id' => '2023-9999',
            'name' => 'JohnDoe',
            'email' => 'johndoe@example.com',
            'password' => bcrypt('password123'),
            'role' => 'student',
        ]);

        // Access homepage with session set to user's ID
        $response = $this->withSession(['user_id' => '2023-9999'])->get('/');

        $response->assertStatus(200);
        $response->assertSee('Sign in to your account to continue.');
        $response->assertSee('Welcome back, JohnDoe');
        $response->assertSee('Username');
        $response->assertSee('Password');
        $response->assertSee('Sign In');
        $response->assertDontSee('Create Account');
        $response->assertDontSee('Register');
        $response->assertDontSee('Email Address');
    }

    /**
     * Test home page when user is not registered yet (password is null).
     * It should show only the Register form.
     */
    public function test_homepage_shows_register_form_only_when_user_has_no_account(): void
    {
        // Create user with null name, email, and password
        $user = User::create([
            'user_id' => '2023-8888',
            'name' => null,
            'email' => null,
            'password' => null,
            'role' => 'student',
        ]);

        // Access homepage with session set to user's ID
        $response = $this->withSession(['user_id' => '2023-8888'])->get('/');

        $response->assertStatus(200);
        $response->assertSee('Create your account to continue.');
        $response->assertSee('Welcome! Please register your account for ID: 2023-8888');
        $response->assertSee('Email Address');
        $response->assertSee('Create Account');
        $response->assertDontSee('Sign in to your account to continue.');
        $response->assertDontSee('Forgot Password?');
    }

    /**
     * Test login enforces and verifies user_id from the session.
     * If two users have the same name, they are distinguished correctly by session user_id.
     */
    public function test_login_requires_and_matches_user_id_from_session(): void
    {
        // Create two users with the exact same name but different user_ids
        $userA = User::create([
            'user_id' => 'ID-A',
            'name' => 'SameName',
            'email' => 'a@example.com',
            'password' => bcrypt('password123'),
            'role' => 'student',
        ]);

        $userB = User::create([
            'user_id' => 'ID-B',
            'name' => 'SameName',
            'email' => 'b@example.com',
            'password' => bcrypt('password123'),
            'role' => 'teacher',
        ]);

        // 1. Attempt login with session user_id set to 'ID-A'
        $responseA = $this->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class])
            ->withSession(['user_id' => 'ID-A'])
            ->post('/login', [
                'logname' => 'SameName',
                'logpassword' => 'password123',
            ]);

        $this->assertTrue(Auth::check());
        $this->assertEquals('ID-A', Auth::user()->user_id);

        // Logout
        Auth::logout();
        $this->assertFalse(Auth::check());

        // 2. Attempt login with session user_id set to 'ID-B'
        $responseB = $this->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class])
            ->withSession(['user_id' => 'ID-B'])
            ->post('/login', [
                'logname' => 'SameName',
                'logpassword' => 'password123',
            ]);

        $this->assertTrue(Auth::check());
        $this->assertEquals('ID-B', Auth::user()->user_id);
    }

    /**
     * Test login fails or redirects if session user_id is missing.
     */
    public function test_login_fails_if_session_user_id_is_missing(): void
    {
        $response = $this->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class])
            ->post('/login', [
                'logname' => 'SomeName',
                'logpassword' => 'password123',
            ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors(['id']);
        $this->assertFalse(Auth::check());
    }

    /**
     * Test registration automatically sends verification code and redirects to verification route.
     */
    public function test_registration_sends_verification_code_and_redirects(): void
    {
        // Create unregistered user
        $user = User::create([
            'user_id' => 'ID-C',
            'name' => null,
            'email' => null,
            'password' => null,
            'role' => 'student',
        ]);

        $response = $this->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class])
            ->withSession(['user_id' => 'ID-C'])
            ->post('/register', [
                'name' => 'NewUser',
                'email' => 'newuser@example.com',
                'password' => 'password123',
            ]);

        // Registration should redirect to the verification notice route
        $response->assertRedirect(route('verification.notice'));
        $response->assertSessionHas('success', 'A verification code has been sent to your email.');
        $response->assertSessionHas('code_sent', true);
        $response->assertSessionHas('verified_email', 'newuser@example.com');

        // Check user details are saved
        $user->refresh();
        $this->assertEquals('NewUser', $user->name);
        $this->assertEquals('newuser@example.com', $user->email);
        $this->assertNotNull($user->password);

        // Check code was stored in cache for the ID-C user
        $cachedCode = Cache::get('verify_code_ID-C');
        $this->assertNotNull($cachedCode);
        $this->assertEquals(6, strlen($cachedCode));
    }

    /**
     * Test admin can toggle capstone stage enabled status.
     */
    public function test_admin_can_toggle_capstone_stage_enabled_status(): void
    {
        $admin = User::create([
            'user_id' => 'admin-99',
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $stage = CapstoneStages::create([
            'id' => 1,
            'stage_title' => 'Capstone 1',
            'is_enabled' => true,
        ]);

        $response = $this->actingAs($admin)
            ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class])
            ->post(route('admin.toggle_capstone_stage'), [
                'stage_id' => 1,
            ]);

        $response->assertStatus(302);
        $stage->refresh();
        $this->assertFalse($stage->is_enabled);
    }

    /**
     * Test admin dashboard renders with system directory detail modal container.
     */
    public function test_admin_dashboard_renders_with_detail_modal(): void
    {
        $admin = User::create([
            'user_id' => 'admin-99',
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.page'));

        $response->assertStatus(200);
        $response->assertSee('id="dashboard_detail_modal"', false);
        $response->assertSee('openDashboardDetailModal', false);
    }

    /**
     * Test admin can archive groups under a capstone stage by year.
     */
    public function test_admin_can_archive_groups_by_year(): void
    {
        $admin = User::create([
            'user_id' => 'admin-99',
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $stage = CapstoneStages::create([
            'id' => 2,
            'stage_title' => 'Capstone 2',
            'is_enabled' => true,
        ]);

        $teacherUser = User::create([
            'user_id' => 'teacher-99',
            'name' => 'Teacher',
            'email' => 'teacher@test.com',
            'password' => bcrypt('password123'),
            'role' => 'teacher',
            'email_verified_at' => now(),
        ]);

        $teacher = Teacher::create([
            'user_id' => 'teacher-99',
            'teacher_first_name' => 'John',
            'teacher_last_name' => 'Smith',
            'teacher_email' => 'teacher@test.com',
            'contact_number' => '09111111111',
        ]);

        $section = Section::create([
            'section_name' => 'IT4A',
            'user_id' => 'teacher-99',
        ]);

        $group = Group::create([
            'group_name' => 'Archivable Group',
            'capstone_title' => 'Title Here',
            'adviser_id' => $teacher->id,
            'section_id' => $section->id,
            'capstone_stage_id' => 2,
            'is_archived' => false,
        ]);

        // Force set the created_at to a specific year
        \Illuminate\Support\Facades\DB::table('groups')
            ->where('id', $group->id)
            ->update(['created_at' => '2025-05-01 12:00:00']);

        $response = $this->actingAs($admin)
            ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class])
            ->post(route('admin.archive_capstone_by_year'), [
                'stage_id' => 2,
                'year' => 2025,
                'new_title' => 'Capstone 2 - New Cycle',
            ]);

        $response->assertStatus(302);
        $group->refresh();
        $this->assertTrue($group->is_archived);
        $this->assertEquals(2025, $group->archived_year);
    }

    /**
     * Test verification code sending fails if the requested email is already taken by another user.
     */
    public function test_send_verification_code_fails_if_email_is_already_taken_after_login(): void
    {
        // User A (logged in, has some other email)
        $userA = User::create([
            'user_id' => 'user-a-123',
            'name' => 'User A',
            'email' => 'usera@test.com',
            'password' => bcrypt('password123'),
            'role' => 'student',
        ]);

        // User B (already has the email we want to verify)
        $userB = User::create([
            'user_id' => 'user-b-456',
            'name' => 'User B',
            'email' => 'taken@test.com',
            'password' => bcrypt('password123'),
            'role' => 'student',
        ]);

        $response = $this->actingAs($userA)
            ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class])
            ->post(route('verification.send_code'), [
                'email' => 'taken@test.com',
            ]);

        $response->assertSessionHasErrors(['email' => 'This email is already taken.']);
    }

    /**
     * Test verification code confirmation fails if the email has been taken by another user.
     */
    public function test_confirm_verification_code_fails_if_email_is_already_taken(): void
    {
        // User A (logged in, has some other email)
        $userA = User::create([
            'user_id' => 'user-a-789',
            'name' => 'User A',
            'email' => 'usera2@test.com',
            'password' => bcrypt('password123'),
            'role' => 'student',
        ]);

        // User B (already has the email we want to verify)
        $userB = User::create([
            'user_id' => 'user-b-012',
            'name' => 'User B',
            'email' => 'taken2@test.com',
            'password' => bcrypt('password123'),
            'role' => 'student',
        ]);

        // Store code in Cache
        Cache::put('verify_code_user-a-789', '123456', now()->addMinutes(10));

        $response = $this->actingAs($userA)
            ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class])
            ->post(route('verification.confirm'), [
                'email' => 'taken2@test.com',
                'code' => '123456',
            ]);

        $response->assertSessionHasErrors(['email' => 'This email is already taken.']);
    }
}
