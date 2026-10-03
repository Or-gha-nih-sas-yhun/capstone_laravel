<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\user_controller;
use App\Http\Controllers\admin_controller;
use Illuminate\Support\Facades\Artisan;

Route::get('/clear-cache', function() {
    Artisan::call('optimize:clear');
    return 'Cache cleared!';
});
/*
|--------------------------------------------------------------------------
| Home Route – Guest only, redirects logged‑in users to their dashboard
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (Auth::check()) {
        return match (Auth::user()->role) {
            'admin'   => redirect()->route('admin.page'),
            'teacher' => redirect()->route('teacher.page'),
            'student' => redirect()->route('student.page'),
            default   => redirect()->route('home.page'),
        };
    }
    return view('home', ['greetings' => 'Hello, welcome to the home page!']);
})->name('login');

// Redirect any GET requests to /login back to the main home page
Route::redirect('/login', '/');

/*
|--------------------------------------------------------------------------
| Authentication Routes – Public
|--------------------------------------------------------------------------
*/
Route::post('/register',   [user_controller::class, 'register']);
Route::post('/login',      [user_controller::class, 'login']);
Route::post('/logout',     [user_controller::class, 'logout'])->name('logout');
Route::post('/id',         [user_controller::class, 'id'])->name('id.check');
Route::post('/destroy',    [user_controller::class, 'destroy'])->name('destroy.session');
Route::post('/send-code',  [user_controller::class, 'sendVerificationCode'])
    ->name('send_code')
    ->middleware('throttle:5,1');

Route::get('/forgot-password',          [user_controller::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password/send',    [user_controller::class, 'sendForgotPasswordCode'])->name('password.email');
Route::post('/forgot-password/reset',   [user_controller::class, 'resetPasswordWithCode'])->name('password.update');
Route::get('/password/reset-confirmation', [user_controller::class, 'showResetConfirmation'])->name('password.reset.confirmation');

/*
|--------------------------------------------------------------------------
| Protected Routes – Authentication Required
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Global Routes (accessible by any authenticated user)
    |--------------------------------------------------------------------------
    */
    Route::get('/verify-email',          [user_controller::class, 'showVerifyEmailForm'])->name('verification.notice');
    Route::post('/verify-email/send',    [user_controller::class, 'sendVerificationCodeAfterLogin'])->name('verification.send_code');
    Route::post('/verify-email/confirm', [user_controller::class, 'confirmVerificationCode'])->name('verification.confirm');

    Route::get('/certificate/{groupId}/{certificateId}', [user_controller::class, 'showCertificate'])->name('certificate.show');
    Route::get('/group/{groupId}/certificates',          [user_controller::class, 'getGroupCertificates'])->name('group.certificates');

    /*
    |--------------------------------------------------------------------------
    | Admin Routes – Role: admin
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->middleware(['role:admin'])->group(function () {

        // Teacher management
        Route::post('/add_teacher',   [admin_controller::class, 'addTeacher'])->name('admin.add_teacher');
        Route::post('/edit_teacher',  [admin_controller::class, 'editTeacher'])->name('admin.edit_teacher');
        Route::post('/delete_teacher',[admin_controller::class, 'deleteTeacher'])->name('admin.delete_teacher');

        // Student management
        Route::post('/add_student',   [admin_controller::class, 'addStudent'])->name('admin.add_student');
        Route::post('/edit_student',  [admin_controller ::class, 'editStudent'])->name('admin.edit_student');
        Route::post('/delete_student',[admin_controller::class, 'deleteStudent'])->name('admin.delete_student');

        // Rubrics & Milestones
        Route::post('/add_rubric',    [admin_controller::class, 'addRubric'])->name('admin.add_rubric');
        Route::get('/get-rubric/{id}',[admin_controller::class, 'getRubric'])->name('admin.get_rubric');
        Route::put('/update-rubric/{id}', [admin_controller::class, 'updateRubric'])->name('admin.update_rubric');
        Route::post('/delete-rubrics',[admin_controller::class, 'deleteRubrics'])->name('admin.delete_rubrics');

        Route::post('/add_milestone', [admin_controller::class, 'addMilestone'])->name('admin.add_milestone');
        Route::get('/get-milestone/{id}', [admin_controller::class, 'getMilestone'])->name('admin.get_milestone');
        Route::put('/update-milestone/{id}', [admin_controller::class, 'updateMilestone'])->name('admin.update_milestone');
        Route::post('/reorder-milestones', [admin_controller::class, 'reorderMilestones'])->name('admin.reorder_milestones');
        Route::post('/delete-milestone', [admin_controller::class, 'deleteMilestone'])->name('admin.delete_milestone');

        // Groups & Advisers
        Route::post('/assign-group',  [admin_controller::class, 'assignGroups'])->name('admin.assign_group');
        Route::post('/assign-section',[admin_controller::class, 'assignSection'])->name('admin.assign_section');
        Route::post('/create-group',  [user_controller::class, 'createGroup'])->name('admin.create_group');
        Route::get('/get-group/{id}', [admin_controller::class, 'getGroupAdmin'])->name('admin.get_group');
        Route::get('/get-group-progress/{groupId}', [user_controller::class, 'getGroupProgress'])->name('admin.get_group_progress');
        Route::post('/delete-group', [admin_controller::class, 'deleteGroup'])->name('admin.delete_group');

        Route::put('/update-group/{id}', [admin_controller::class, 'updateGroupAdmin'])->name('admin.update_group');
        Route::get('/get-students/{section}', [user_controller::class, 'getStudentsBySection'])->name('admin.get-students');

        // Teacher assignments & lookups
        Route::get('/teacher-groups/{teacherId}',    [admin_controller::class, 'getTeacherGroups'])->name('admin.teacher_groups');
        Route::get('/teacher-sections/{teacherId}',  [admin_controller::class, 'getTeacherSections'])->name('admin.teacher.sections');

        // Bulk import
        Route::post('/import-students',  [admin_controller::class, 'importStudents'])->name('admin.import_students');
        Route::get('/download-student-template', [admin_controller::class, 'downloadStudentTemplate'])->name('admin.download_student_template');
        Route::post('/import-teachers',  [admin_controller::class, 'importTeachers'])->name('admin.import_teachers');
        Route::get('/download-teacher-template', [admin_controller::class, 'downloadTeacherTemplate'])->name('admin.download_teacher_template');

        // Evaluation rooms
        Route::post('/evaluation-rooms',        [admin_controller::class, 'createRoom'])->name('admin.create_room');
        Route::get('/evaluation-rooms', function () {
            return redirect()->route('admin.page');
        });
        Route::get('/get-room/{room}',          [admin_controller::class, 'getRoom'])->name('admin.get_room');
        Route::post('/evaluation-rooms/{room}/panelists', [admin_controller::class, 'addPanelist'])->name('admin.add_panelist');
        Route::delete('/evaluation-rooms/{room}/panelists/{teacher}', [admin_controller::class, 'removePanelist'])->name('admin.remove_panelist');
        Route::post('/delete_room', [admin_controller::class, 'deleteRoom'])->name('admin.delete_room');
        Route::post('/evaluation-rooms/{roomId}/regenerate-code', [admin_controller::class, 'regenerateRoomCode'])->name('admin.regenerate_room_code');

        // Admin profile
        Route::post('/profile_update',       [admin_controller::class, 'adminProfileUpdate'])->name('admin.profile_update');
        Route::post('/update-password',      [user_controller::class, 'updatePassword'])->name('admin.profile.update_password');

        // Capstone management
        Route::post('/capstone/toggle-stage', [admin_controller::class, 'toggleCapstoneStage'])->name('admin.toggle_capstone_stage');
        Route::post('/capstone/archive',      [admin_controller::class, 'archiveCapstoneByYear'])->name('admin.archive_capstone_by_year');
        Route::post('/capstone/enable-year',  [admin_controller::class, 'enableCapstoneYear'])->name('admin.enable_capstone_year');
        Route::post('/capstone/add-year',     [admin_controller::class, 'addCapstoneYear'])->name('admin.add_capstone_year');
        Route::post('/capstone/restore-group/{id}', [admin_controller::class, 'restoreGroup'])->name('admin.restore_group');
        Route::post('/capstone/delete-archived-group/{id}', [admin_controller::class, 'deleteArchivedGroup'])->name('admin.delete_archived_group');
        Route::post('/capstone/add-stage', [admin_controller::class, 'addCapstoneStage'])->name('admin.add_capstone_stage');
        Route::post('/capstone/update-stage/{id}', [admin_controller::class, 'updateCapstoneStage'])->name('admin.update_capstone_stage');
        Route::post('/capstone/delete-stage/{id}', [admin_controller::class, 'deleteCapstoneStage'])->name('admin.delete_capstone_stage');
        Route::post('/capstone/activate-year/{id}', [admin_controller::class, 'activateCapstoneYear'])->name('admin.activate_capstone_year');
        Route::post('/capstone/archive-year/{id}', [admin_controller::class, 'archiveCapstoneYear'])->name('admin.archive_capstone_year');
        Route::post('/capstone/update-year/{id}', [admin_controller::class, 'updateCapstoneYear'])->name('admin.update_capstone_year');
        Route::post('/capstone/delete-year/{id}', [admin_controller::class, 'deleteCapstoneYear'])->name('admin.delete_capstone_year');
        Route::get('/get-group-revisions/{groupId}',[user_controller::class, 'adminGetGroupRevisions'])->name('admin.get_group_revisions');
        Route::get('/get-recommendation-sheet/{groupId}',
    [user_controller::class, 'teacherGetRecommendationSheet'])
    ->name('admin.get_recommendation_sheet');

Route::get('/get-approval-sheet/{groupId}',
    [user_controller::class, 'teacherGetApprovalSheet'])
    ->name('admin.get_approval_sheet');
    });

    /*
    |--------------------------------------------------------------------------
    | Teacher Routes – Role: teacher
    |--------------------------------------------------------------------------
    */
    Route::prefix('teacher')->middleware(['role:teacher'])->group(function () {
    Route::get('/get-recommendation-sheet/{groupId}', [user_controller::class, 'teacherGetRecommendationSheet']);
    Route::get('/get-approval-sheet/{groupId}',       [user_controller::class, 'teacherGetApprovalSheet']);

        Route::post('/issue-recommendation-sheet', [user_controller::class, 'issueRecommendationSheet'])->name('teacher.issue_recommendation');
        Route::get('/get-recommendation-status/{groupId}', [user_controller::class, 'getRecommendationStatus'])->name('teacher.recommendation_status');


Route::post('/issue-sheet',
    [user_controller::class, 'issueSheet'])->name('teacher.issue_sheet');

Route::get('/get-sheet-status/{groupId}',
    [user_controller::class, 'getSheetStatus'])->name('teacher.sheet_status');
        // Profile
        Route::post('/profile_update',    [user_controller::class, 'profileUpdate'])->name('teacher.profile_update');
        Route::post('/update-password',   [user_controller::class, 'updatePassword'])->name('teacher.update_password');

        // Students & Groups (teacher's own)
        Route::get('/get-students/{section}', [user_controller::class, 'getStudentsBySection'])->name('teacher.get_students');
        Route::post('/create-group',          [user_controller::class, 'createGroup'])->name('teacher.create_group');
        Route::get('/get-group/{group}',      [user_controller::class, 'getGroupDetails'])->name('teacher.get_group');
        Route::post('/update-group/{group}',  [user_controller::class, 'updateGroup'])->name('teacher.update_group');

        // Rubrics & evaluations
        Route::get('/get-rubric/{milestone}', [user_controller::class, 'getRubricForMilestone'])->name('teacher.rubric');
        Route::post('/submit-evaluation',     [user_controller::class, 'submitEvaluation'])->name('teacher.submit_evaluation');

        // Remark evaluation (adviser or panelist)
        Route::post('/evaluate-remark', [user_controller::class, 'evaluateMilestoneRemark'])->name('teacher.evaluate_remark');
        Route::post('/update-remark', [user_controller::class, 'updateMilestoneRemark'])
        ->middleware('auth')
        ->name('teacher.update_remark');

        // Milestone evaluation status
        Route::get('/get-evaluated-milestones/{group}', [user_controller::class, 'getEvaluatedMilestones'])->name('teacher.evaluated_milestones');

        // Group progress (view modal)
        Route::get('/get-group-progress/{group}', [user_controller::class, 'getGroupProgress'])->name('teacher.group_progress');

        // Join an evaluation room as panelist
        Route::post('/join-room', [user_controller::class, 'joinRoomWithCode'])->name('teacher.join_room');

        // Revision routes
        Route::post('/group/{group}/request-revision', [user_controller::class, 'requestGroupRevision'])->name('teacher.request_revision');
        Route::post('/group/{group}/mark-revised',      [user_controller::class, 'markGroupRevised'])->name('teacher.mark_revised');
        // Fetch revision details for a group (used by the verification modal)
        Route::get('/get-revision-details/{group}', [user_controller::class, 'getRevisionDetails'])->name('teacher.get_revision_details');

        // Submit revision verification (checklist + approval)
        Route::post('/group/{group}/verify-revision', [user_controller::class, 'verifyRevision'])->name('teacher.verify_revision');

        Route::get('/get-my-evaluation/{groupId}', [App\Http\Controllers\user_controller::class, 'getMyEvaluation'])->name('teacher.get_my_evaluation');
        Route::get('/get-all-revisions/{groupId}', [user_controller::class, 'getAllRevisionsForGroup'])->name('teacher.get_all_revisions');
    });

    /*
    |--------------------------------------------------------------------------
    | Student Routes – Role: student
    |--------------------------------------------------------------------------
    */
    // Dashboard
    Route::get('/sections/student', [user_controller::class, 'dashboard'])->name('student.page');

    Route::prefix('student')->middleware(['role:student'])->group(function () {
        Route::post('/update',               [user_controller::class, 'update'])->name('student.profile.update');
        Route::post('/update-password',      [user_controller::class, 'updatePassword'])->name('student.profile.update_password');
        Route::get('/get-group-progress/{group}', [user_controller::class, 'getGroupProgress'])->name('student.group_progress');

        // Revision sheet endpoints
        Route::get('/get-group/{groupId}', [user_controller::class, 'getStudentGroup'])->name('student.group.details');
        Route::get('/get-revision/{groupId}/{revisionId}', [user_controller::class, 'getStudentRevisionById'])->name('student.revision.details');
        Route::get('/get-approval-sheet/{groupId}', [user_controller::class, 'getApprovalSheet'])->name('student.approval_sheet');
        Route::get('/get-recommendation-sheet/{groupId}', [user_controller::class, 'getRecommendationSheet'])
    ->middleware(['auth', 'role:student'])
    ->name('student.get-recommendation-sheet');
    });

    /*
    |--------------------------------------------------------------------------
    | Public Dashboard Access (roles already checked by controller)
    |--------------------------------------------------------------------------
    */
    Route::get('/sections/admin',   [user_controller::class, 'adminDashboard'])->name('admin.page');
    Route::get('/sections/teacher', [user_controller::class, 'teacherDashboard'])->name('teacher.page');
});