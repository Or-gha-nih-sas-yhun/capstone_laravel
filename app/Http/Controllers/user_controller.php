<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Unique;
use App\Models\Evaluation;
use App\Models\Certificate;
use App\Models\GroupCertificate;
use App\Models\GroupMilestones;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use App\Models\Rubric;
use App\Models\Milestone;
use App\Models\RubricCriteria;
use App\Models\Student;
use App\Models\Group;
use App\Models\TeamMember;
use Illuminate\Contracts\Support\ValidatedData;
use App\Models\Teacher;
use App\Models\Admin;
use App\Models\EvaluationRoom;
use App\Models\Section;
use App\Models\CapstoneStages;
use App\Models\CapstoneYear;
use Illuminate\Support\Facades\Hash;
use App\Imports\StudentsImport;
use App\Imports\TeachersImport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon; // Add this at the top of your controller
use App\Services\Mailer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;


class user_controller extends Controller
{
    // ── ROLE REDIRECT HELPER ──────────────────────────────────────
    private function redirectByRole()
    {
        return match(Auth::user()->role) {
            'admin'   => redirect()->route('admin.page'),
            'teacher' => redirect()->route('teacher.page'),
            default   => redirect()->route('student.page'),
        };
    }

    // ── REGISTER ──────────────────────────────────────────────────
    public function register(Request $request)
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }

        $user = User::where('user_id', session('user_id'))->first();

        if (!$user) {
            return back()->withErrors(['id' => 'No matching ID found. Please check your ID again.']);
        }

        if (!is_null($user->password)) {
            return back()->withErrors(['id' => 'This account has already been registered. Please log in instead.']);
        }

        $incomingdata = $request->validate([
            'name'     => ['required', 'min:3'],
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        $user->name     = $incomingdata['name'];
        $user->email    = $incomingdata['email'];
        $user->password = bcrypt($incomingdata['password']);
        $user->save();

        Auth::login($user);

        // ── GENERATE & SEND EMAIL VERIFICATION CODE AFTER CREATING ACCOUNT ──
        $code = (string) random_int(100000, 999999);
        Cache::put('verify_code_' . $user->user_id, $code, now()->addMinutes(10));

        Mailer::send(
            $user->email,
            $user->user_id,
            'Your Capstone Tracker verification code',
            "<p>Your verification code is:</p><h2>{$code}</h2><p>This code expires in 10 minutes.</p>"
        );

        return redirect()->route('verification.notice')
            ->with('success', 'A verification code has been sent to your email.')
            ->with('code_sent', true)
            ->with('verified_email', $user->email);
    }

    // ── VERIFICATION ─────────────────────────────────────────────
    public function sendVerificationCode(Request $request)
    {   
        $request->validate(['email' => 'required|email']);

        $user = User::where('user_id', session('user_id'))->first();
        if (!$user) {
            return back()->withErrors(['id' => 'No matching ID found. Please check your ID again.']);
        }

        if (!is_null($user->password)) {
            return back()->withErrors(['id' => 'This account has already been registered. Please log in instead.']);
        }

        // Find the email already on file for this role — this is the identity check.
        $onFileEmail = match ($user->role) {
            'student' => Student::where('user_id', $user->user_id)->value('student_email'),
            'teacher' => Teacher::where('user_id', $user->user_id)->value('teacher_email'),
            'admin'   => Admin::where('user_id', $user->user_id)->value('admin_email'),
            default   => null,
        };

        if (!$onFileEmail || strtolower($onFileEmail) !== strtolower($request->email)) {
            return back()->withErrors(['email' => 'This email does not match our records for this ID.']);
        }

        $code = (string) random_int(100000, 999999);
        Cache::put('verify_code_' . $user->user_id, $code, now()->addMinutes(10));

        $sent = Mailer::send(
            $onFileEmail,
            $user->user_id,
            'Your Capstone Tracker verification code',
            "<p>Your verification code is:</p><h2>{$code}</h2><p>This code expires in 10 minutes.</p>"
        );

        if (!$sent) {
            return back()->withErrors(['email' => 'Failed to send verification email. Please try again.']);
        }

        return back()->with('success', 'A verification code has been sent to your email.')
                      ->with('code_sent', true)
                      ->with('verified_email', $request->email);
    }

    // ── LOGIN ─────────────────────────────────────────────────────
    public function login(Request $request)
    {
        // Block if already logged in
        if (Auth::check()) {
            return $this->redirectByRole();
        }

        if (!session('user_id')) {
            return redirect('/')->withErrors(['id' => 'Please enter your ID first.']);
        }

        
     

        $incomingdata = $request->validate([
                'logname'     => 'required',
                'logpassword' => 'required'
            ]);

        if (Auth::attempt([
            'user_id'     => session('user_id'),
            'name'        => $incomingdata['logname'],
            'password'    => $incomingdata['logpassword']
        ])) {
            $request->session()->regenerate();
            return $this->redirectByRole()->with('success', 'Log in successful. Welcome back, ' . Auth::user()->role . '!');
        }

        return back()->withErrors([
            'logname' => 'invalid username /password',
        ]);
    }

    // ── LOGOUT ───────────────────────────────────────────────────
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->forget('user_id');  // ← clears home page session
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    // ── DESTROY SESSION (Change User) ────────────────────────────
    public function destroy(Request $request)
    {
        Auth::logout();                          // ← also clears auth
        $request->session()->forget('user_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    // ── ID CHECK ─────────────────────────────────────────────────
    public function id(Request $request)
    {
        $request->validate(['id' => 'required']);

        $user = User::where('user_id', $request->id)->first();

        if (!$user) {
            return back()->withErrors(['id' => 'User ID not found.']);
        }

        session(['user_id' => $request->id]);
        return redirect('/');
    }
    
    /**
     * If a certificate is configured for this milestone, issue it to the group
     * (idempotent — won't create a duplicate if it's already been issued).
     */
private function autoIssueCertificateIfEligible($groupId, $milestoneId)
{
    $certificate = Certificate::where('milestone_id', $milestoneId)->first();
    if (!$certificate) {
        return;
    }

    $alreadyIssued = GroupCertificate::where('group_id', $groupId)
        ->where('certificate_id', $certificate->id)
        ->exists();

    if ($alreadyIssued) {
        return; // idempotent — don't touch its existing serial
    }

    GroupCertificate::create([
        'group_id'       => $groupId,
        'certificate_id' => $certificate->id,
        'issued_date'    => now()->toDateString(),
        'serial_number'  => $this->generateSerialNumber($certificate->document_type ?? 'approval'), // ← changed
    ]);
}


    // ── STUDENT DASHBOARD ─────────────────────────────────────────
    public function dashboard()
    {
        $user = Auth::user();
        if (!$user) return redirect('/');
        if ($user->role !== 'student') {
            return $this->redirectByRole();
        }

        $student = Student::where('user_id', $user->user_id)->first();
        $groups = $student?->groups()->first();
        $members = collect();
        $adviser = Teacher::where('user_id', $groups?->adviser_id)->first();
        $completedMilestoneIds = [];
        $remarksByMilestone = collect();
        $absencesByMilestone = collect();
        if ($groups) {
            $remarksByMilestone = \App\Models\Remarks::where('group_id', $groups->id)
                ->get()
                ->keyBy('milestone_id');

            $absencesByMilestone = \App\Models\Absence::where('group_id', $groups->id)
                ->get()
                ->groupBy('milestone_id');
        }

        if ($groups) {
            $groups->load(['team_members.student.user', 'adviser.user', 'groupMilestones']);
            $members = $groups->team_members;
            $adviser = $groups->adviser;
            $completedMilestoneIds = $groups->groupMilestones
                ->where('status', 'completed')
                ->pluck('milestone_id')
                ->toArray();
        }

 
      // ── Load ALL revisions for this group (if any) ──
    $revisions = collect();
    if ($groups) {
        $revisions = \App\Models\Revision::with([
            'documentation',
            'enhancements',
            'objectives',
            'panelist'  // eager load the panelist teacher
        ])
        ->where('group_id', $groups->id)
        ->orderBy('created_at', 'desc')
        ->get();
    }


 
        $activeYear = CapstoneYear::getActiveYear();
        $groupYearId = $groups ? $groups->capstone_year_id : $activeYear->id;

        $enabledStageIds = CapstoneStages::where('is_enabled', true)
            ->where('capstone_year_id', $groupYearId)
            ->pluck('id');
        $milestones = Milestone::whereIn('capstone_stage_id', $enabledStageIds)->orderBy('step_order')->get();
        $overallProgress = 0;
        $nextMilestone = null;

        if ($groups) {
            $enabledMilestoneIds = $milestones->pluck('id')->toArray();
            $completedMilestoneIds = $groups->groupMilestones
                ->where('status', 'completed')
                ->whereIn('milestone_id', $enabledMilestoneIds)
                ->pluck('milestone_id')
                ->toArray();
            $completed = count($completedMilestoneIds);
            $overallProgress = $milestones->count() ? round(($completed / $milestones->count()) * 100) : 0;
            // Next = first milestone (by step order) not yet completed for this group
            $nextMilestone = $milestones->first(fn($m) => !in_array($m->id, $completedMilestoneIds));
        }

        $evaluations = Evaluation::where('group_id', $groups?->id)
            ->with('milestone', 'teacher.user')
            ->latest('evaluation_date')
            ->limit(5)
            ->get()
            ->map(function ($e) {
                $decoded = json_decode($e->feedback, true);
                if (is_array($decoded) && isset($decoded['feedback_text'])) {
                    $e->feedback = $decoded['feedback_text'];
                }
                return $e;
            });

        $allEvaluations = $groups
            ? Evaluation::where('group_id', $groups->id)
                ->with(['milestone', 'teacher.user'])
                ->latest('evaluation_date')
                ->get()
                ->map(function ($e) {
                    $decoded = json_decode($e->feedback, true);
                    if (is_array($decoded) && isset($decoded['feedback_text'])) {
                        $e->feedback = $decoded['feedback_text'];
                        $e->rubric_scores = $decoded['rubric_scores'] ?? [];
                    } else {
                        $e->rubric_scores = [];
                    }
                    return $e;
                })
            : collect();
            
        $groupcertificates = $groups
            ? GroupCertificate::where('group_id', $groups->id)->get()
            : collect();

            // in dashboard(), replace the certificates map with:
            $certificates = Certificate::all()->map(function ($cert) use ($groupcertificates, $groups) {
                $groupCert = $groupcertificates->firstWhere('certificate_id', $cert->id);
                $cert->unlocked = (bool) $groupCert;
                $cert->issued_date = $groupCert->issued_date ?? null;
                return $cert;
            });

   // ── Check if capstone is fully completed ──
    $isCapstoneComplete = false;
    if ($groups) {
        $totalMilestones = $milestones->count();
        $completedMilestones = count($completedMilestoneIds);
        $isCapstoneComplete = ($totalMilestones > 0 && $completedMilestones == $totalMilestones);
    }
    // ── Check if Capstone 2 is fully completed ──
$isCapstone2Complete = false;
if ($groups) {
    $capstone2Milestones = $milestones->filter(function($m) {
        return $m->capstoneStage && $m->capstoneStage->stage_type == 2;
    });
    $capstone2Total = $capstone2Milestones->count();
    if ($capstone2Total > 0) {
        $capstone2MilestoneIds = $capstone2Milestones->pluck('id')->toArray();
        $capstone2Completed = $groups->groupMilestones
            ->where('status', 'completed')
            ->whereIn('milestone_id', $capstone2MilestoneIds)
            ->count();
        $isCapstone2Complete = ($capstone2Completed == $capstone2Total);
    }
}


       $certificatesCap1 = collect();
$certificatesCap2 = collect();

foreach ($certificates as $cert) {
    // Each certificate is linked to a milestone via `milestone_id`
    $milestone = \App\Models\Milestone::find($cert->milestone_id);
    if ($milestone) {
        $stage = $milestone->capstoneStage->stage_type ?? null;
        if ($stage == 1) {
            $certificatesCap1->push($cert);
        } elseif ($stage == 2) {
            $certificatesCap2->push($cert);
        }
    }
}

// ── Has this group's required capstone milestone (oral presentation) been evaluated? ──
$isRequiredCapstoneEvaluated = false;
if ($groups && $groups->room && $groups->room->required_milestone_id) {
    $isRequiredCapstoneEvaluated = \App\Models\Evaluation::where('group_id', $groups->id)
        ->where('milestone_id', $groups->room->required_milestone_id)
        ->exists();
}

// ── Locate the "Approval Sheet" milestone for Capstone 2 (e.g. "ISSUANCE OF APPROVAL SHEET") ──
$approvalMilestone = $milestones->first(function ($m) {
    return $m->capstoneStage
        && $m->capstoneStage->stage_type == 2
        && stripos($m->milestone_title, 'approval sheet') !== false;
});

// ── Has this group ever had a revision requested? ──
$groupRevisions = collect();
if ($groups) {
    $groupRevisions = \App\Models\Revision::with(['documentation', 'enhancements', 'objectives'])
        ->where('group_id', $groups->id)
        ->get();
}

// ── Path 1: the "ISSUANCE OF APPROVAL SHEET" milestone itself is completed ──
$isApprovalMilestoneComplete = $approvalMilestone
    ? in_array($approvalMilestone->id, $completedMilestoneIds)
    : false;

// ── Path 2: a revision was requested AND every item across all revisions is "Completed" ──
$isRevisionFullyCleared = $groupRevisions->isNotEmpty() && $groupRevisions->every(function ($rev) {
    $allDocsDone = $rev->documentation->every(fn($item) => strtolower(trim($item->remarks ?? '')) === 'completed');
    $allEnhDone  = $rev->enhancements->every(fn($item) => strtolower(trim($item->remarks ?? '')) === 'completed');
    $allObjDone  = $rev->objectives->every(fn($item) => strtolower(trim($item->remarks ?? '')) === 'completed');
    return $allDocsDone && $allEnhDone && $allObjDone;
});

// ── Unlocked if EITHER path is satisfied — not exclusive ──
$isApprovalSheetUnlocked = $isApprovalMilestoneComplete || $isRevisionFullyCleared;

// ── Recommendation Sheet unlocks: the "Recommendation Sheet" milestone itself is marked completed ──
$isRecommendationUnlocked = false;
if ($groups) {
    $recommendationMilestone = $milestones->first(function ($m) {
        return stripos($m->milestone_title, 'recommendation sheet') !== false;
    });
    if ($recommendationMilestone) {
        $isRecommendationUnlocked = in_array($recommendationMilestone->id, $completedMilestoneIds);
    }
}
        return view('sections.student', compact(
            'user', 'student', 'groups', 'members', 'adviser',
            'milestones', 'overallProgress', 'nextMilestone',
            'evaluations', 'allEvaluations', 'certificates', 'completedMilestoneIds', 'groupcertificates',
            'remarksByMilestone', 'absencesByMilestone','revisions','isCapstoneComplete','isCapstone2Complete','isRecommendationUnlocked','isApprovalSheetUnlocked','certificatesCap1','certificatesCap2'
        ));
    }

    // ── ADMIN DASHBOARD ──────────────────────────────────────────
 public function adminDashboard()
{
    $user = Auth::user();
    if (!$user) return redirect('/');
    if ($user->role !== 'admin') {
        return $this->redirectByRole();
    }
    $admin = Admin::where('user_id', $user->user_id)->first();

    // ── Active Capstone Year & Auto-population ──
    $activeYear = CapstoneYear::getActiveYear();

    // Ensure any pre-existing/legacy database rows are mapped to the active year
    Group::whereNull('capstone_year_id')->update(['capstone_year_id' => $activeYear->id]);
    Student::whereNull('capstone_year_id')->update(['capstone_year_id' => $activeYear->id]);
    CapstoneStages::whereNull('capstone_year_id')->update(['capstone_year_id' => $activeYear->id]);

    $capstoneYears = CapstoneYear::withCount(['groups', 'students'])->get();

    // ── Core data ──
    $enabledStageIds = CapstoneStages::where('is_enabled', true)
        ->where('capstone_year_id', $activeYear->id)
        ->pluck('id');
    $milestones = Milestone::whereIn('capstone_stage_id', $enabledStageIds)->with('rubrics')->orderBy('step_order')->get();
    $rubrics = Rubric::whereHas('milestone', function($q) use ($enabledStageIds) {
        $q->whereIn('capstone_stage_id', $enabledStageIds);
    })->with(['milestone', 'criteria'])->latest()->get();
    $groups = Group::where('is_archived', false)
        ->where('capstone_year_id', $activeYear->id)
        ->with(['students', 'groupMilestones'])
        ->get();
    $archivedGroups = Group::where('is_archived', true)->with(['adviser', 'section', 'team_members', 'capstoneStage'])->get();
    $totalGroups = $groups->count();

    $allTeachers = Teacher::where('is_archived', false)->with([
        'groups' => function ($query) use ($activeYear) {
            $query->where('is_archived', false)->where('capstone_year_id', $activeYear->id);
        },
        'user',
        'sections' => function ($query) {
            $query->where('is_archived', false);
        }
    ])->get();
    $totalTeachers = $allTeachers->count();

    $sections = Section::where('is_archived', false)->get();
    $totalSections = $sections->count();
    $allSections = Section::all();

    // ── Students with their group (singular) ──
    $allStudents = Student::where('is_archived', false)
        ->where('capstone_year_id', $activeYear->id)
        ->with(['user', 'groups'])
        ->get();
    $totalStudents = $allStudents->count();

    // evaluationroom
    $evaluationRooms = EvaluationRoom::where('is_archived', false)
        ->with([
        'panelists',
        'groups' => function ($query) use ($activeYear) {
            $query->where('is_archived', false)->where('capstone_year_id', $activeYear->id);
        },
        'requiredMilestone'
    ])->latest()->get();

    // ── Prepare enabled milestone IDs for progress calculations ──
    $enabledMilestoneIds = $milestones->pluck('id')->toArray();
    $totalMilestones = $milestones->count();

    // ── Build per‑section group progress lists ──
    foreach ($sections as $section) {
        $groupsInSection = $groups->filter(function($group) use ($section) {
            return $group->section_id === $section->id;
        });

        $groupList = [];
        foreach ($groupsInSection as $group) {
            $completed = $group->groupMilestones
                ->where('status', 'completed')
                ->whereIn('milestone_id', $enabledMilestoneIds)
                ->count();
            $progress = $totalMilestones > 0 ? round(($completed / $totalMilestones) * 100) : 0;

            $groupList[] = (object) [
                 'id'       => $group->id,
                'name'     => $group->group_name,
                'progress' => $progress,
                'status'   => $progress >= 70 ? 'On Track' : ($progress >= 40 ? 'At Risk' : 'Delayed'),
                'color'    => $progress >= 70 ? '#1e6b3a' : ($progress >= 40 ? '#b88d3a' : '#a12b2b'),
            ];
        }

        // Attach the group list to the section object
        $section->groups = $groupList;
    }

    // ── Section Progress (now with groups attached) ──
    $sectionProgress = [];
    $milestoneCount = $milestones->count();

    foreach ($sections as $section) {
        // Get students in this section
        $studentUserIds = Student::where('section', $section->section_name)->pluck('user_id');
        // Get group IDs of those students
        $groupIds = TeamMember::whereIn('user_id', $studentUserIds)->pluck('group_id')->unique();
        // Get groups with their milestones
        $groupsInSection = Group::where('is_archived', false)->whereIn('id', $groupIds)->with('groupMilestones')->get();

        $total = $groupsInSection->count();
        $done = 0;
        $inProgress = 0;
        $notStarted = 0;
        $totalProgress = 0;

        foreach ($groupsInSection as $group) {
            $completed = $group->groupMilestones
                ->where('status', 'completed')
                ->whereIn('milestone_id', $enabledMilestoneIds)
                ->count();
            $progress = $milestoneCount > 0 ? ($completed / $milestoneCount) * 100 : 0;
            $totalProgress += $progress;

            if ($completed == $milestoneCount) {
                $done++;
            } elseif ($completed > 0) {
                $inProgress++;
            } else {
                $notStarted++;
            }
        }

        $avg = $total > 0 ? round($totalProgress / $total) : 0;

        $sectionProgress[] = (object) [
            'name'        => $section->section_name,
            'done'        => $done,
            'in_progress' => $inProgress,
            'not_started' => $notStarted,
            'avg'         => $avg,
            'groups'      => $section->groups, // <-- group list attached
        ];
    }

    // ── Milestone Completion ──
    $milestoneCompletion = [];
    $colors = ['#d6b15c', '#b88d3a', '#8b6914', '#5b6375', '#0a1428'];

    foreach ($milestones->values() as $i => $m) {
        $completedCount = GroupMilestones::where('milestone_id', $m->id)
                                        ->where('status', 'completed')
                                        ->count();
        $milestoneCompletion[] = (object) [
            'name'      => $m->milestone_title,
            'completed' => $completedCount,
            'total'     => $totalGroups,
            'color'     => $colors[$i % count($colors)],
            'stage'     => $m->capstone_stage_id,
        ];
    }

    // ── Overall Stats ──
    $onTrackCount = 0;
    $atRiskCount  = 0;
    $delayedCount = 0;
    $totalProgressSum = 0;

    foreach ($groups as $group) {
        $completed = $group->groupMilestones
            ->where('status', 'completed')
            ->whereIn('milestone_id', $enabledMilestoneIds)
            ->count();
        $progress = $milestoneCount > 0 ? ($completed / $milestoneCount) * 100 : 0;
        $totalProgressSum += $progress;
        if ($progress >= 70) {
            $onTrackCount++;
        } elseif ($progress >= 40) {
            $atRiskCount++;
        } else {
            $delayedCount++;
        }
    }
    $avgProgress = $totalGroups > 0 ? round($totalProgressSum / $totalGroups) : 0;

    // ── Capstone Completion Progress (real data, per milestone) ──
    $progressItems = [];
    $progressColors = ['#d6b15c', '#b88d3a', '#8b6914', '#5b6375', '#0a1428'];
    foreach ($milestones->take(5) as $i => $m) {
        $completedCount = GroupMilestones::where('milestone_id', $m->id)
            ->where('status', 'completed')
            ->count();
        $pct = $totalGroups > 0 ? round(($completedCount / $totalGroups) * 100) : 0;

        $progressItems[] = (object) [
            'label' => $m->milestone_title,
            'done'  => $completedCount,
            'total' => $totalGroups,
            'pct'   => $pct,
            'color' => $progressColors[$i % count($progressColors)],
        ];
    }

    // ── Sections Data for Assign Modal ──
    $sectionsWithTeachers = Section::leftJoin('teachers', 'sections.user_id', '=', 'teachers.user_id')
        ->select('sections.*', 'teachers.teacher_first_name', 'teachers.teacher_last_name')
        ->get();

    $capstone1Milestones = Milestone::whereHas('capstoneStage', function ($q) {
        $q->where('stage_type', 1)->where('is_archived', false);
    })->pluck('id')->toArray();

    $capstone2Milestones = Milestone::whereHas('capstoneStage', function ($q) {
        $q->where('stage_type', 2)->where('is_archived', false);
    })->pluck('id')->toArray();

    $groupsData = Group::where('is_archived', false)->where('capstone_year_id', $activeYear->id)
        ->with(['adviser', 'section', 'students', 'room', 'groupMilestones', 'team_members'])
        ->get()
        ->map(function ($group) use ($capstone1Milestones, $capstone2Milestones) {
            $completedMilestoneIds = $group->groupMilestones
                ->where('status', 'completed')
                ->pluck('milestone_id')
                ->toArray();

            $completedC1 = false;
            if (count($capstone1Milestones) > 0) {
                $completedC1 = empty(array_diff($capstone1Milestones, $completedMilestoneIds));
            }

            $completedC2 = false;
            if (count($capstone2Milestones) > 0) {
                $completedC2 = empty(array_diff($capstone2Milestones, $completedMilestoneIds));
            }

            $members = $group->students->map(function ($student) {
                return [
                    'name' => $student->student_first_name . ' ' . $student->student_last_name,
                    'user_id' => $student->user_id,
                ];
            })->toArray();

            return [
                'id'                    => $group->id,
                'name'                  => $group->group_name,
                'capstone_title'        => $group->capstone_title,
                'section_id'            => $group->section_id,
                'section_name'          => $group->section->section_name ?? 'N/A',
                'assigned_teacher_id'   => $group->adviser->user_id ?? null,
                'assigned_teacher_name' => $group->adviser
                    ? $group->adviser->teacher_first_name . ' ' . $group->adviser->teacher_last_name
                    : null,
                'member_count'          => $group->team_members->count(),
                'room_id'               => $group->room_id,
                'room_name'             => $group->room->room_name ?? 'Unassigned',
                'members'               => $members,
                'completed_c1'          => $completedC1,
                'completed_c2'          => $completedC2,
            ];
        });

    $sectionsData = $sectionsWithTeachers->map(function($section) {
        return [
            'id'                     => $section->id,
            'name'                   => $section->section_name,
            'assigned_teacher_id'    => $section->user_id,
            'assigned_teacher_name'  => $section->user_id
                ? $section->teacher_first_name . ' ' . $section->teacher_last_name
                : null,
        ];
    });

    $capstoneStages = CapstoneStages::where('is_archived', false)
        ->where('capstone_year_id', $activeYear->id)
        ->get();

    // ── Recent Activities (real data) ──
    $recentActivities = collect();

    foreach (Rubric::latest()->take(5)->get() as $rubric) {
        $recentActivities->push([
            'icon' => 'fa-check', 'color' => '#1e6b3a',
            'title' => 'New rubric created',
            'subtitle' => $rubric->rubric_name,
            'timestamp' => $rubric->created_at,
        ]);
    }

    foreach (Evaluation::with('group', 'milestone')->latest()->take(5)->get() as $eval) {
        $recentActivities->push([
            'icon' => 'fa-check', 'color' => '#1e6b3a',
            'title' => 'Group evaluated',
            'subtitle' => ($eval->group->group_name ?? 'Group') . ' — ' . ($eval->milestone->milestone_title ?? 'Milestone'),
            'timestamp' => $eval->created_at,
        ]);
    }

    foreach (Student::latest()->take(5)->get() as $student) {
        $recentActivities->push([
            'icon' => 'fa-arrow-right', 'color' => '#0a1428',
            'title' => 'Student registered',
            'subtitle' => $student->student_first_name . ' ' . $student->student_last_name . ' (' . $student->user_id . ')',
            'timestamp' => $student->created_at,
        ]);
    }

    foreach (Teacher::latest()->take(5)->get() as $teacher) {
        $recentActivities->push([
            'icon' => 'fa-arrow-right', 'color' => '#0a1428',
            'title' => 'Teacher added',
            'subtitle' => $teacher->teacher_first_name . ' ' . $teacher->teacher_last_name,
            'timestamp' => $teacher->created_at,
        ]);
    }

    $upcoming = Milestone::whereBetween('due_date', [now(), now()->addDays(3)])->get();
    foreach ($upcoming as $m) {
        $pendingCount = Group::where('is_archived', false)->whereDoesntHave('groupMilestones', function ($q) use ($m) {
            $q->where('milestone_id', $m->id)->where('status', 'completed');
        })->count();
        if ($pendingCount > 0) {
            $recentActivities->push([
                'icon' => 'fa-clock', 'color' => '#8a5d0b',
                'title' => 'Deadline reminder',
                'subtitle' => $m->milestone_title . ' due for ' . $pendingCount . ' group(s)',
                'timestamp' => now()->subMinute(),
            ]);
        }
    }

    $recentActivities = $recentActivities->sortByDesc('timestamp')->take(8)->values();

    // ── Active/Enabled Capstone Year ──
    $enabledYear = $activeYear->year;
    $allCapstoneYears = CapstoneYear::pluck('year')->toArray();

    // ── Group progress list (used elsewhere, keep it) ──
    $groupProgressList = [];
    foreach ($groups as $group) {
        $completed = $group->groupMilestones
            ->where('status', 'completed')
            ->whereIn('milestone_id', $enabledMilestoneIds)
            ->count();
        $progress = $totalMilestones > 0 ? round(($completed / $totalMilestones) * 100) : 0;

        $status = 'On Track';
        if ($progress < 40) $status = 'Delayed';
        elseif ($progress < 70) $status = 'At Risk';

        $groupProgressList[] = (object) [
            'group_name'       => $group->group_name,
            'capstone_title'   => $group->capstone_title,
            'section'          => $group->section->section_name ?? 'N/A',
            'adviser'          => $group->adviser ? $group->adviser->teacher_first_name . ' ' . $group->adviser->teacher_last_name : 'Unassigned',
            'progress'         => $progress,
            'completed'        => $completed,
            'total'            => $totalMilestones,
            'status'           => $status,
            'status_color'     => $progress >= 70 ? '#1e6b3a' : ($progress >= 40 ? '#b88d3a' : '#a12b2b'),
        ];
    }



    $issuedDocuments = GroupCertificate::with(['certificate', 'group.students.user'])
    ->orderByDesc('issued_date')
    ->get()
    ->map(function ($gc) {
        $group = $gc->group;
        return [
            'id'                 => $gc->id,
            'serial_number'      => $gc->serial_number ?? '—',
            'certificate_title'  => $gc->certificate->certificate_title ?? 'Document',
            'group_name'         => $group->group_name ?? 'Unknown Group',
            'section_name'       => optional($group?->students->first())->section ?? 'N/A',
            'issued_date'        => $gc->issued_date,
        ];
    });
    // ── Return view with all compact variables ──
    return view('sections.admin', compact(
        'user',
        'milestones',
        'rubrics',
        'sectionProgress',
        'milestoneCompletion',
        'progressItems',
        'onTrackCount',
        'atRiskCount',
        'delayedCount',
        'avgProgress',
        'admin',
        'allTeachers',
        'allStudents',
        'allSections',
        'recentActivities',
        'evaluationRooms',
        'sections',
        'groupsData',
        'sectionsData',
        'totalStudents',
        'totalGroups',
        'totalTeachers',
        'totalSections',
        'capstoneStages',
        'archivedGroups',
        'enabledYear',
        'allCapstoneYears',
        'activeYear',
        'capstoneYears',
        'groupProgressList',
        'issuedDocuments'
    ));
}





    // ── TEACHER DASHBOARD ─────────────────────────────────────────
   public function teacherDashboard()
{
    $user = Auth::user();
    if (!$user) return redirect('/');
    if ($user->role !== 'teacher') {
        return $this->redirectByRole();
    }

    $teacher = Teacher::where('user_id', $user->user_id)->first();
    if (!$teacher) {
        return redirect('/')->with('error', 'Teacher profile not found.');
    }

    $activeYear = CapstoneYear::getActiveYear();

    $assignedRooms = $teacher->evaluationRooms()->with('groups')->get();
    $assignedRoomIds = $assignedRooms->pluck('id');
    $teacherId = $teacher->id;
    // ── All evaluation rooms, for the classroom grid ──   
    $allRooms = EvaluationRoom::where('is_archived', false)->
    with(['panelists', 'groups'])->get();           

    $groups = Group::where('is_archived', false)
        ->where('capstone_year_id', $activeYear->id)
        ->where(function($query) use ($teacher, $assignedRoomIds) {
            $query->where('adviser_id', $teacher->id)
                  ->orWhereIn('room_id', $assignedRoomIds);
        })
        ->with(['students', 'groupMilestones', 'team_members', 'room'])
        ->get();
    
        $adviserGroups = $groups->where('adviser_id', $teacher->id)->values();

    $requestedGroupIds = \App\Models\Revision::where('panelist_id', $teacherId)
                                        ->whereIn('group_id', $groups->pluck('id'))
                                        ->pluck('group_id')
                                        ->toArray();

    foreach ($groups as $group) {
        $group->has_requested_revision = in_array($group->id, $requestedGroupIds);
    }

    $totalGroups = $adviserGroups->count();
    $teacherSections = $teacher->sections;
    $sectionIdsWithGroups = $adviserGroups->pluck('section_id')->filter()->unique();
    $sectionsWithGroups = Section::whereIn('id', $sectionIdsWithGroups)->get();
    $totalStudents = $adviserGroups->flatMap(fn($g) => $g->students)->unique('id')->count();

    $enabledStageIds = CapstoneStages::where('is_enabled', true)
        ->where('capstone_year_id', $activeYear->id)
        ->pluck('id');
    $milestones = Milestone::whereIn('capstone_stage_id', $enabledStageIds)->orderBy('step_order')->get();
    $milestoneCount = $milestones->count();

    $groupProgress = [];
    $enabledMilestoneIds = $milestones->pluck('id')->toArray();

    foreach ($adviserGroups as $group) {
        $completed = $group->groupMilestones
            ->where('status', 'completed')
            ->whereIn('milestone_id', $enabledMilestoneIds)
            ->count();
        $progress = $milestoneCount > 0 ? round(($completed / $milestoneCount) * 100) : 0;

        $groupProgress[] = (object) [
            'group_name' => $group->group_name ?? 'Unnamed Group',
            'capstone_title' => $group->capstone_title ?? 'No title',
            'progress' => $progress,
            'completed' => $completed,
            'total' => $milestoneCount,
            'id' => $group->id,
        ];
    }

    $evaluations = Evaluation::where('teacher_id', $teacher->id)
        ->with(['group', 'milestone'])
        ->latest('evaluation_date')
        ->limit(10)
        ->get()
        ->map(function ($e) {
            $decoded = json_decode($e->feedback, true);
            if (is_array($decoded) && isset($decoded['feedback_text'])) {
                $e->feedback = $decoded['feedback_text'];
            }
            return $e;
        });

    $totalEvaluations = Evaluation::where('teacher_id', $teacher->id)->count();

    $pendingEvaluations = Evaluation::where('teacher_id', $teacher->id)
        ->whereNull('score')
        ->count();

    if ($pendingEvaluations == 0) {
        $pendingEvaluations = $groups->filter(function($group) use ($milestoneCount, $enabledMilestoneIds) {
            $completed = $group->groupMilestones
                ->where('status', 'completed')
                ->whereIn('milestone_id', $enabledMilestoneIds)
                ->count();
            return $completed < $milestoneCount;
        })->count();
    }

    $sections = [];
    foreach ($groups as $group) {
        $firstStudent = $group->students->first();
        $sectionName = $firstStudent ? ($firstStudent->section ?? 'Unassigned') : 'Unassigned';
        if (!isset($sections[$sectionName])) {
            $sections[$sectionName] = [];
        }
        $sections[$sectionName][] = $group;
    }

    $allSections = Section::all();
    $allGroups = Group::where('is_archived', false)
        ->where('capstone_year_id', $activeYear->id)
        ->where(function($query) use ($teacher, $assignedRoomIds) {
            $query->where('adviser_id', $teacher->id)
                  ->orWhereIn('room_id', $assignedRoomIds);
        })
        ->with(['students', 'groupMilestones'])
        ->get();

    return view('sections.teacher', compact(
        'user', 'teacher', 'groups', 'totalGroups', 'totalStudents',
        'milestones', 'groupProgress', 'evaluations', 'totalEvaluations',
        'pendingEvaluations', 'sections', 'teacherSections', 'allSections',
        'allGroups', 'sectionsWithGroups', 'assignedRooms', 'allRooms','adviserGroups'
    ));
}

    // ── GET STUDENTS BY SECTION (unassigned only) ──────────────────
    public function getStudentsBySection($sectionName)
    {
        $activeYear = CapstoneYear::getActiveYear();

        $students = Student::where('section', $sectionName)
            ->where('capstone_year_id', $activeYear->id)
            ->whereDoesntHave('teamMembers')
            ->with('user')
            ->get(['user_id', 'student_first_name', 'student_last_name']); // include user_id
        return response()->json($students);
    }

    // ── CREATE GROUP (admin/teacher shared) ─────────────────────────
    public function createGroup(Request $request)
    {
        $validated = $request->validate([
            'group_name'         => 'required|string|max:255|unique:groups,group_name',
            'capstone_title'     => 'required|string|max:255|unique:groups,capstone_title',
            'section'         => 'required|exists:sections,id',
            'students'           => 'required|array|min:1|max:5',
            'students.*.user_id' => 'exists:students,user_id',
            'students.*.role'    => 'required|string|in:programmer,designer,researcher',
            'adviser'            => 'required|exists:teachers,id'
        ]);

        foreach ($validated['students'] as $studentData) {
            $student = Student::where('user_id', $studentData['user_id'])->first();
            if ($student && $student->teamMembers()->exists()) {
                return back()
                    ->withErrors(['students' => "Student {$student->student_first_name} {$student->student_last_name} is already in a group."])
                    ->withInput();
            }
        }

        $activeYear = CapstoneYear::getActiveYear();

        $group = Group::create([
            'group_name'     => $validated['group_name'],
            'capstone_title' => $validated['capstone_title'],
            'adviser_id'     => $validated['adviser'],
            'section_id'     => $validated['section'],
            'capstone_year_id' => $activeYear->id,
        ]);

        foreach ($validated['students'] as $studentData) {
            TeamMember::create([
                'group_id' => $group->id,
                'user_id'  => $studentData['user_id'],
                'role'     => $studentData['role'],
            ]);
        }

        $enabledStageIds = CapstoneStages::where('is_enabled', true)
            ->where('capstone_year_id', $activeYear->id)
            ->pluck('id');
        foreach (Milestone::whereIn('capstone_stage_id', $enabledStageIds)->get() as $milestone) {
            GroupMilestones::firstOrCreate(
                ['group_id' => $group->id, 'milestone_id' => $milestone->id],
                ['status' => 'pending', 'due_date' => $milestone->due_date]
            );
        }

        return $this->redirectByRole()->with('success', 'Group created successfully!');
    }



    // ── TEACHER PROFILE UPDATE ──────────────────────────────────────
    public function profileUpdate(Request $request)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'teacher') {
            return redirect('/');
        }

        $teacher = Teacher::where('user_id', $user->user_id)->first();

        if (!$teacher) {
            return redirect('/')->with('error', 'Teacher profile not found.');
        }

        $validatedData = $request->validate([
            'teacher_first_name' => 'required|string|max:255',
            'teacher_last_name' => 'required|string|max:255',
            'teacher_middle_name' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'teacher_email' => 'required|email|unique:teachers,teacher_email,' . $teacher->id,
        ]);

        $teacher->update($validatedData);

        $teacher->teacher_first_name = $validatedData['teacher_first_name'];
        $teacher->teacher_middle_name = $validatedData['teacher_middle_name'];
        $teacher->teacher_last_name = $validatedData['teacher_last_name'];
        $teacher->contact_number = $validatedData['contact_number'];
        $teacher->teacher_email = $validatedData['teacher_email'];

        $teacher->save();

        return redirect()->route('teacher.page')->with('success', 'Profile updated successfully.');
    }



    // ── STUDENT PROFILE UPDATE ──────────────────────────────────────
    public function update(Request $request)
    {
        $user = User::find(Auth::id());
        if (!$user || $user->role !== 'student') {
            return redirect('/');
        }

        $student = Student::where('user_id', $user->user_id)->first();
        if (!$student) {
            return redirect('/')->with('error', 'Student profile not found.');
        }

        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:students,student_email,' . $student->id . '|unique:users,email,' . $user->id,
            'phone'      => 'nullable|string|max:20',
        ]);

        $student->student_first_name = $validatedData['first_name'];
        $student->student_last_name  = $validatedData['last_name'];
        $student->student_email      = $validatedData['email'];
        $student->contact_number     = $validatedData['phone'] ?? '';
        $student->save();

        if ($user->email !== $validatedData['email']) {
            $user->email = $validatedData['email'];
            $user->save();
        }

        return redirect()->route('student.page')->with('success', 'Profile updated successfully.');
    }



    /**
     * Return the rubric criteria for a given milestone as JSON.
     */
    public function getRubricForMilestone($milestoneId)
    {
        $rubric = Rubric::where('milestone_id', $milestoneId)
                    ->with('criteria')
                    ->first();

        if (!$rubric) {
            return response()->json(['error' => 'No rubric found for this milestone.'], 404);
        }

        return response()->json([
            'rubric_name' => $rubric->rubric_name,
            'criteria'    => $rubric->criteria->map(function ($c) {
                return [
                    'id'            => $c->id,
                    'criteria_name' => $c->criteria_name,
                    'weight'        => $c->weight,
                    'max_score'     => $c->max_score,
                ];
            }),
        ]);
    }

    /**
     * Store a RUBRIC-SCORE evaluation for a group (one record per group per milestone).
     *
     * AUTHORIZATION: Panelist ONLY. A teacher must be assigned as a panelist to the
     * evaluation room this group belongs to. Being the group's adviser is NOT enough
     * here — advisers without a panelist seat cannot submit rubric scores.
     */
    public function submitEvaluation(Request $request)
    {
        $validated = $request->validate([
            'group_id'        => 'required|exists:groups,id',
            'milestone_id'    => 'required|exists:milestones,id',
            'score'           => 'required|numeric|min:0',
            'max_score'       => 'required|numeric|min:0',
            'feedback'        => 'nullable|string',
            'attendance'      => 'required|in:present,absent',
            'absent_students' => 'nullable|array',
            'absent_students.*' => 'exists:students,user_id',
            'feedback1'       => 'nullable|string',
            'rubric_scores'   => 'nullable|array',
        ]);

        $teacher = Teacher::where('user_id', Auth::user()->user_id)->firstOrFail();
        $group = Group::findOrFail($validated['group_id']);
        
        // ==========================================
// BLOCK EVALUATION IF REVISION IS NOT COMPLETE
// ==========================================

$revision = \App\Models\Revision::with([
    'documentation',
    'enhancements',
    'objectives'
])
->where('group_id', $group->id)
->where('panelist_id', $teacher->id)
->first();

if ($revision) {

    $hasPendingDocumentation = $revision->documentation
        ->contains(function ($item) {
            return strtolower($item->remarks ?? 'pending') !== 'completed';
        });

    $hasPendingEnhancements = $revision->enhancements
        ->contains(function ($item) {
            return strtolower($item->remarks ?? 'pending') !== 'completed';
        });

    $hasPendingObjectives = $revision->objectives
        ->contains(function ($item) {
            return strtolower($item->remarks ?? 'pending') !== 'completed';
        });


    $hasPendingRevision =
        $hasPendingDocumentation ||
        $hasPendingEnhancements ||
        $hasPendingObjectives;


    if ($hasPendingRevision) {

        return back()->with(
            'error',
            'You cannot evaluate this group yet. Please verify all revision items as Completed first.'
        );
    }
}
        // Rubric scoring is restricted to panelists assigned to this group's room —
        // adviser status alone does NOT grant access here.
        $assignedRoomIds = $teacher->evaluationRooms()->pluck('evaluation_rooms.id')->toArray();
        if (!in_array($group->room_id, $assignedRoomIds)) {
            return back()->with('error', 'You are not authorized to evaluate this group because it is not in your assigned classrooms.');
        }

        $milestone = Milestone::findOrFail($validated['milestone_id']);

        // Validate group has students
        $firstMember = $group->team_members()->first();
        if (!$firstMember) {
            return back()->with('error', 'Group has no students.');
        }
        $studentId = $firstMember->user_id;

        // Save rubric scores and feedback text in JSON format in the feedback column
        $feedbackPayload = json_encode([
            'feedback_text' => $validated['feedback'] ?? '',
            'rubric_scores' => $request->input('rubric_scores', []),
        ]);

        // ── 1. Save the Evaluation (Score) ──
        Evaluation::updateOrCreate(
            [
                'group_id'     => $validated['group_id'],
                'milestone_id' => $validated['milestone_id'],
                'teacher_id'   => $teacher->id,
            ],
            [
                'student_id'      => $studentId,
                'score'           => $validated['score'],
                'max_score'       => $validated['max_score'],
                'feedback'        => $feedbackPayload,
                'evaluation_date' => now()->toDateString(),
            ]
        );

        // ── 2. Calculate Auto-Remarks based on Milestone Dates ──
        $evaluationDate = Carbon::now();
        $dueDate = Carbon::parse($milestone->due_date);
        $startDate = $milestone->start_date ? Carbon::parse($milestone->start_date) : null;

        $isCompiled = false;
        $deduction = 0;
        $remarksStatus = '';

        if ($startDate && $evaluationDate->lessThan($startDate)) {
            $isCompiled = true;
            $deduction = 0;
            $remarksStatus = 'Early Submission';
        } elseif ($evaluationDate->lessThanOrEqualTo($dueDate)) {
            $isCompiled = true;
            $deduction = 0;
            $remarksStatus = 'On Time Compliance';
        } else {
            $isCompiled = false;
            $daysLate = $evaluationDate->diffInDays($dueDate);
            $deduction = $daysLate * 10; // 10 points per day late
            $remarksStatus = "Late Submission ({$daysLate} day(s) late)";
        }

        // ── 3. Save the Remarks (Attendance & Auto-Compliance) ──
        \App\Models\Remarks::updateOrCreate(
            [
                'group_id'     => $validated['group_id'],
                'milestone_id' => $validated['milestone_id'],
            ],
            [
                'adviser_id'       => (string) $teacher->id,
                'all_present'      => $validated['attendance'] === 'present',
                'compiled'         => $isCompiled,
                'deduction_points' => $deduction,
                'feedback'         => $validated['feedback1'] ?? '',
                'remarks'          => $remarksStatus,
                'date_evaluated'   => now(),
            ]
        );

        // ── 4. Track Absences (if any) ──
        if ($validated['attendance'] === 'absent' && !empty($validated['absent_students'])) {
            \App\Models\Absence::where('group_id', $validated['group_id'])
                ->where('milestone_id', $validated['milestone_id'])
                ->delete();

            foreach ($validated['absent_students'] as $absentUserId) {
                \App\Models\Absence::create([
                    'group_id'     => $validated['group_id'],
                    'milestone_id' => $validated['milestone_id'],
                    'user_id'      => $absentUserId,
                ]);
            }
        }

        // ── 5. Mark the Group's Milestone as Completed ──
        \App\Models\GroupMilestones::updateOrCreate(
            ['group_id' => $validated['group_id'], 'milestone_id' => $validated['milestone_id']],
            ['status' => 'completed', 'completion_date' => now()->toDateString()]
        );
        
        // Reset Group Revision Status
        $group->update([
            'revision_status' => 'none',
            'revision_description' => null,
        ]);

        // ── 6. Auto-issue certificate if this milestone has one ──
        $this->autoIssueCertificateIfEligible($validated['group_id'], $validated['milestone_id']);

        return redirect()->route('teacher.page')->with('success', 'Evaluation saved successfully! System marked it as ' . $remarksStatus);
    }

    /**
     * Store a REMARK evaluation (attendance + auto-compliance) for a group/milestone.
     *
     * AUTHORIZATION: Adviser OR panelist. Unlike submitEvaluation (rubric scoring),
     * a teacher who advises this group can do this even if they are not assigned
     * to any evaluation room / panelist seat for it.
     */
    public function evaluateMilestoneRemark(Request $request)
    {
        $validated = $request->validate([
            'group_id'           => 'required|exists:groups,id',
            'milestone_id'       => 'required|exists:milestones,id',
            'attendance'         => 'required|in:present,absent',
            'absent_students'    => 'nullable|array',
            'absent_students.*'  => 'exists:students,user_id',
            'feedback'           => 'nullable|string',
        ]);

        $teacher = Teacher::where('user_id', Auth::user()->user_id)->firstOrFail();
        $group = Group::findOrFail($validated['group_id']);

        $assignedRoomIds = $teacher->evaluationRooms()->pluck('evaluation_rooms.id')->toArray();
        $isPanelist = in_array($group->room_id, $assignedRoomIds);
        $isAdviser = $group->adviser_id === $teacher->id;

        // Check if the milestone has an associated rubric
        $hasRubric = \App\Models\Rubric::where('milestone_id', $validated['milestone_id'])->exists();

        if ($hasRubric) {
            // ONLY panels can evaluate if there is a rubric
            if (!$isPanelist) {
                return response()->json(['error' => 'Only the panelist of this room is authorized to evaluate this milestone because it has an associated rubric.'], 403);
            }
        } else {
            // BOTH adviser and panelist can evaluate if there is NO rubric
            if (!$isPanelist && !$isAdviser) {
                return response()->json(['error' => 'You are not authorized to evaluate this milestone.'], 403);
            }
        }

        $alreadyCompleted = \App\Models\GroupMilestones::where('group_id', $validated['group_id'])
            ->where('milestone_id', $validated['milestone_id'])
            ->where('status', 'completed')
            ->exists();

        if ($alreadyCompleted) {
            return response()->json(['error' => 'This milestone has already been evaluated.'], 422);
        }

        $milestone = Milestone::findOrFail($validated['milestone_id']);

        $evaluationDate = Carbon::now();
        $dueDate = Carbon::parse($milestone->due_date);
        $startDate = $milestone->start_date ? Carbon::parse($milestone->start_date) : null;

        if ($startDate && $evaluationDate->lessThan($startDate)) {
            // Evaluated before the official start date → Early
            $isCompiled = true;
            $deduction = 0;
            $remarksStatus = 'Early Submission';
        } elseif ($evaluationDate->lessThanOrEqualTo($dueDate)) {
            // On or before due date → On Time
            $isCompiled = true;
            $deduction = 0;
            $remarksStatus = 'On Time Compliance';
        } else {
            // Evaluated after due date → Late
            $isCompiled = false;
            $daysLate = (int) $evaluationDate->diffInDays($dueDate, true); // absolute
            $deduction = $daysLate * 10;
            $remarksStatus = "Late Submission ({$daysLate} day(s) late)";
        }

        $remark = \App\Models\Remarks::create([
            'group_id'         => $validated['group_id'],
            'milestone_id'     => $validated['milestone_id'],
            'adviser_id'       => (string) $teacher->id,
            'all_present'      => $validated['attendance'] === 'present',
            'compiled'         => $isCompiled,
            'deduction_points' => $deduction,
            'feedback'         => $validated['feedback'] ?? '',
            'remarks'          => $remarksStatus,
            'date_evaluated'   => now(),
        ]);

        $absentNames = [];
        if ($validated['attendance'] === 'absent' && !empty($validated['absent_students'])) {
            foreach ($validated['absent_students'] as $absentUserId) {
                \App\Models\Absence::create([
                    'group_id'     => $validated['group_id'],
                    'milestone_id' => $validated['milestone_id'],
                    'user_id'      => $absentUserId,
                ]);
                $s = Student::where('user_id', $absentUserId)->first();
                $absentNames[] = $s ? trim($s->student_first_name.' '.$s->student_last_name) : $absentUserId;
            }
        }

        \App\Models\GroupMilestones::updateOrCreate(
            ['group_id' => $validated['group_id'], 'milestone_id' => $validated['milestone_id']],
            ['status' => 'completed', 'completion_date' => now()->toDateString()]
        );
        $group->update([
            'revision_status' => 'none',
            'revision_description' => null,
        ]);
        // Auto-issue certificate if this milestone has one
        $this->autoIssueCertificateIfEligible($validated['group_id'], $validated['milestone_id']);

        $milestoneCount = Milestone::count();
        $completedCount = \App\Models\GroupMilestones::where('group_id', $validated['group_id'])
            ->where('status', 'completed')->count();
        $overallProgress = $milestoneCount > 0 ? round(($completedCount / $milestoneCount) * 100) : 0;

        return response()->json([
            'success'          => true,
            'overall_progress' => $overallProgress,
            'milestone_id'     => $milestone->id,
            'remarks' => [
                'all_present'      => $remark->all_present,
                'compiled'         => $remark->compiled,
                'deduction_points' => (int) $remark->deduction_points,
                'feedback'         => $remark->feedback,
                'remarks_status'   => $remark->remarks,
            ],
            'absent_students'  => $absentNames,
        ]);
    }
/**
 * Allow the GROUP'S ADVISER to manually override an already-saved remark:
 * change remarks_status (e.g. Late → On Time / Considered), edit deduction points,
 * toggle compiled, and update feedback.
 *
 * AUTHORIZATION: ONLY the group's adviser.
 */
public function updateMilestoneRemark(Request $request)
{
    $validated = $request->validate([
        'group_id'          => 'required|exists:groups,id',
        'milestone_id'      => 'required|exists:milestones,id',
        'remarks_status'    => 'required|string|max:255',
        'deduction_points'  => 'nullable|integer|min:0',
        'compiled'          => 'nullable|boolean',
        'feedback'          => 'nullable|string',
    ]);

    $teacher = Teacher::where('user_id', Auth::user()->user_id)->firstOrFail();
    $group   = Group::findOrFail($validated['group_id']);

    // ONLY the group's adviser may edit remarks.
    if ((int) $group->adviser_id !== (int) $teacher->id) {
        return response()->json([
            'error' => 'Only the group adviser can edit this remark.'
        ], 403);
    }

    $remark = \App\Models\Remarks::where('group_id', $validated['group_id'])
        ->where('milestone_id', $validated['milestone_id'])
        ->first();

    if (!$remark) {
        return response()->json([
            'error' => 'No remark found for this milestone.'
        ], 404);
    }

    // Manual override — status text is now adviser-controlled.
    $remark->remarks          = $validated['remarks_status'];
    $remark->deduction_points = $validated['deduction_points'] ?? 0;
    $remark->compiled         = array_key_exists('compiled', $validated)
        ? (bool) $validated['compiled']
        : $remark->compiled;

    if (array_key_exists('feedback', $validated)) {
        $remark->feedback = $validated['feedback'];
    }

    // If the adviser marks it as "Considered" (or On Time / Early) and clears
    // deductions, treat it as a completed/compiled submission.
    if ($remark->deduction_points === 0
        && in_array(strtolower($remark->remarks), ['on time compliance', 'early submission', 'considered'], true)) {
        $remark->compiled = true;
    }

    $remark->date_evaluated = now();
    $remark->save();

    return response()->json([
        'success' => true,
        'message' => 'Remark updated successfully.',
        'remarks' => [
            'all_present'      => (bool) $remark->all_present,
            'compiled'         => (bool) $remark->compiled,
            'deduction_points' => (int) $remark->deduction_points,
            'feedback'         => $remark->feedback,
            'remarks_status'   => $remark->remarks,
        ],
    ]);
}
    /**
     * Milestone IDs already evaluated for a group (feeds the disabled dropdown options).
     * AUTHORIZATION: Adviser OR panelist.
     */
    public function getEvaluatedMilestones($groupId)
    {
        $teacher = Teacher::where('user_id', Auth::user()->user_id)->firstOrFail();
        $group = Group::findOrFail($groupId);

        $isAdviser = $group->adviser_id === $teacher->id;
        $assignedRoomIds = $teacher->evaluationRooms()->pluck('evaluation_rooms.id')->toArray();
        $isPanelist = in_array($group->room_id, $assignedRoomIds);

        if (!$isAdviser && !$isPanelist) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json(
            Evaluation::where('group_id', $groupId)
                ->where('teacher_id', $teacher->id)
                ->pluck('milestone_id')
        );
    }
    public function getMyEvaluation($groupId)
{
    $teacher = Teacher::where('user_id', Auth::user()->user_id)->firstOrFail();
    $group = Group::with('room.requiredMilestone')->findOrFail($groupId);

    $assignedRoomIds = $teacher->evaluationRooms()->pluck('evaluation_rooms.id')->toArray();
    if (!in_array($group->room_id, $assignedRoomIds)) {
        return response()->json(['error' => 'You are not authorized to view this evaluation.'], 403);
    }

    $milestoneId = $group->room->required_milestone_id ?? null;
    if (!$milestoneId) {
        return response()->json(['error' => 'No milestone configured for this room.'], 404);
    }

    $evaluation = Evaluation::where('group_id', $groupId)
        ->where('milestone_id', $milestoneId)
        ->where('teacher_id', $teacher->id)
        ->first();

    if (!$evaluation) {
        return response()->json(['error' => 'You have not evaluated this group yet.'], 404);
    }

    $decoded = json_decode($evaluation->feedback, true);
    $feedbackText = is_array($decoded) && isset($decoded['feedback_text']) ? $decoded['feedback_text'] : $evaluation->feedback;
    $rubricScores = is_array($decoded) && isset($decoded['rubric_scores']) ? $decoded['rubric_scores'] : [];

    $rubric = Rubric::where('milestone_id', $milestoneId)->with('criteria')->first();
    $criteria = [];
    if ($rubric) {
        foreach ($rubric->criteria as $c) {
            $criteria[] = [
                'criteria_name' => $c->criteria_name,
                'weight'        => $c->weight,
                'max_score'     => $c->max_score,
                'given_score'   => $rubricScores[$c->id] ?? 0,
            ];
        }
    }

    return response()->json([
        'group_name'      => $group->group_name,
        'milestone_title' => $group->room->requiredMilestone->milestone_title ?? 'Milestone',
        'score'           => $evaluation->score,
        'max_score'       => $evaluation->max_score,
        'feedback'        => $feedbackText,
        'evaluation_date' => $evaluation->evaluation_date,
        'criteria'        => $criteria,
    ]);
}

    /**
     * Group details (members, section, etc.) for the teacher-side view/edit modals.
     * AUTHORIZATION: Adviser OR panelist.
     */
    public function getGroupDetails($groupId)
{
    $teacher = Teacher::where('user_id', Auth::user()->user_id)->firstOrFail();
    $group = Group::with('team_members.student.user', 'students', 'room.requiredMilestone')
        ->findOrFail($groupId);

    $isAdviser = $group->adviser_id === $teacher->id;
    $assignedRoomIds = $teacher->evaluationRooms()->pluck('evaluation_rooms.id')->toArray();
    $isPanelist = in_array($group->room_id, $assignedRoomIds);

    $isSectionTeacher = false;
    if ($group->section_id) {
        $isSectionTeacher = \App\Models\Section::where('id', $group->section_id)
            ->where('user_id', $teacher->user_id)
            ->exists();
    }

    if (!$isAdviser && !$isPanelist && !$isSectionTeacher) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    // ✅ Compute ONCE at the top level
    $requiredMilestoneId = $group->room->required_milestone_id ?? null;
    $requiredMilestoneTitle = $group->room->activity_name
        ?? ($group->room->requiredMilestone->milestone_title ?? null);

    return response()->json([
        'id'             => $group->id,
        'group_name'     => $group->group_name,
        'capstone_title' => $group->capstone_title,
        'section'        => $group->students->first()?->section ?? null,

        // ✅ Now visible to the JS at the root of the response
        'required_milestone_id'    => $requiredMilestoneId,
        'required_milestone_title' => $requiredMilestoneTitle,

        'members'        => $group->team_members->map(fn($tm) => [
            'user_id' => $tm->user_id,
            'name'    => trim(
                (optional($tm->student)->student_first_name ?? '') . ' ' .
                (optional($tm->student)->student_last_name ?? '')
            ),
            'role'    => $tm->role,
        ]),
    ]);
}// ── TEACHER: recommendation sheet payload ──
public function teacherGetRecommendationSheet($groupId)
{
    $user = Auth::user();
    if (!$user || $user->role !== 'teacher') {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $teacher = Teacher::where('user_id', $user->user_id)->firstOrFail();
    $group   = Group::with(['team_members.student', 'adviser'])->findOrFail($groupId);

    $isAdviser = (int) $group->adviser_id === (int) $teacher->id;
    $assignedRoomIds = $teacher->evaluationRooms()->pluck('evaluation_rooms.id')->toArray();
    $isPanelist = $group->room_id && in_array($group->room_id, $assignedRoomIds);
    $isSectionTeacher = $group->section_id && \App\Models\Section::where('id', $group->section_id)
        ->where('user_id', $teacher->user_id)->exists();

    if (!$isAdviser && !$isPanelist && !$isSectionTeacher) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $members = $group->team_members->map(function ($tm) {
        $s = $tm->student;
        return trim(($s->student_first_name ?? '') . ' ' . ($s->student_last_name ?? ''));
    })->filter()->values()->all();

    $adviser = $group->adviser
        ? trim($group->adviser->teacher_first_name . ' ' . $group->adviser->teacher_last_name)
        : null;

    // Look up the issued GroupCertificate so we can surface the serial + date
    $record = GroupCertificate::where('group_id', $groupId)
        ->whereHas('certificate', fn($q) =>
            $q->where('document_type', 'recommendation')
              ->orWhere('certificate_title', 'like', '%Recommendation%'))
        ->latest('issued_date')
        ->first();

    return response()->json([
        'capstone_title' => $group->capstone_title,
        'members'        => $members,
        'adviser'        => $adviser,
        'date_issued'    => $record?->issued_date,
        'serial_number'  => $record?->serial_number,
        'group_name'     => $group->group_name,
    ]);
}

// ── TEACHER: approval sheet payload ──
public function teacherGetApprovalSheet($groupId)
{
    $user = Auth::user();
    if (!$user || $user->role !== 'teacher') {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $teacher = Teacher::where('user_id', $user->user_id)->firstOrFail();
    $group   = Group::with(['team_members.student', 'adviser', 'room.panelists'])->findOrFail($groupId);

    $isAdviser = (int) $group->adviser_id === (int) $teacher->id;
    $assignedRoomIds = $teacher->evaluationRooms()->pluck('evaluation_rooms.id')->toArray();
    $isPanelist = $group->room_id && in_array($group->room_id, $assignedRoomIds);
    $isSectionTeacher = $group->section_id && \App\Models\Section::where('id', $group->section_id)
        ->where('user_id', $teacher->user_id)->exists();

    if (!$isAdviser && !$isPanelist && !$isSectionTeacher) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $members = $group->team_members->map(function ($tm) {
        $s = $tm->student;
        return trim(($s->student_first_name ?? '') . ' ' . ($s->student_last_name ?? ''));
    })->filter()->values()->all();

    $adviser = $group->adviser
        ? trim($group->adviser->teacher_first_name . ' ' . $group->adviser->teacher_last_name)
        : null;

    $panelists = [];
    if ($group->room) {
        foreach ($group->room->panelists as $p) {
            $panelists[] = [
                'name' => trim($p->teacher_first_name . ' ' . $p->teacher_last_name),
                'role' => $p->pivot->role ?? 'Member',
            ];
        }
    }

    // Oral exam result
    $oralExamResult = '—';
    $oralExamDate   = null;
    $oralMilestone = \App\Models\Milestone::where('milestone_title', 'like', '%Oral Presentation%')->first();
    if ($oralMilestone) {
        $eval = Evaluation::where('group_id', $groupId)
            ->where('milestone_id', $oralMilestone->id)
            ->latest('evaluation_date')->first();
        if ($eval) {
            $oralExamResult = $eval->score >= ($eval->max_score * 0.6) ? 'PASSED' : 'FAILED';
            $oralExamDate = $eval->evaluation_date
                ? \Carbon\Carbon::parse($eval->evaluation_date)->format('F d, Y') : null;
        }
    }

   $record = GroupCertificate::where('group_id', $groupId)
    ->whereHas('certificate', function ($q) {
        $q->where('document_type', 'approval')
          ->orWhere('certificate_title', 'like', '%Approval%');
    })
    ->latest('issued_date')
    ->first();

    return response()->json([
        'capstone_title'   => $group->capstone_title,
        'members'          => $members,
        'adviser'          => $adviser,
        'panelists'        => $panelists,
        'oral_exam_result' => $oralExamResult,
        'oral_exam_date'   => $oralExamDate,
        'school_president' => 'DR. FLORIPIS A. MONTECILLO, Ed.D.',
        'serial_number'    => $record?->serial_number,
    ]);
}

    /**
     * Edit team members for a group (teacher side).
     * AUTHORIZATION: Adviser OR panelist.
     */
    public function updateGroup(Request $request, $groupId)
    {
        $teacher = Teacher::where('user_id', Auth::user()->user_id)->firstOrFail();
        $group = Group::findOrFail($groupId);

        $isAdviser = $group->adviser_id === $teacher->id;
        $assignedRoomIds = $teacher->evaluationRooms()->pluck('evaluation_rooms.id')->toArray();
        $isPanelist = in_array($group->room_id, $assignedRoomIds);

        if (!$isAdviser && !$isPanelist) {
            return back()->with('error', 'You are not authorized to update this group.');
        }

        $validated = $request->validate([
            'group_name'         => 'required|string|max:255|unique:groups,group_name,' . $group->id,
            'capstone_title'     => 'required|string|max:255|unique:groups,capstone_title,' . $group->id,
            'students'           => 'required|array|min:2',
            'students.*.user_id' => 'exists:students,user_id',
            'students.*.role'    => 'required|string|in:programmer,designer,researcher',
        ]);

        foreach ($validated['students'] as $studentData) {
            $inOtherGroup = TeamMember::where('user_id', $studentData['user_id'])
                ->where('group_id', '!=', $group->id)
                ->exists();
            if ($inOtherGroup) {
                $student = Student::where('user_id', $studentData['user_id'])->first();
                return back()
                    ->withErrors(['students' => "Student {$student->student_first_name} {$student->student_last_name} is already in another group."])
                    ->withInput();
            }
        }

        $group->update([
            'group_name'     => $validated['group_name'],
            'capstone_title' => $validated['capstone_title'],
        ]);

        TeamMember::where('group_id', $group->id)->delete();
        foreach ($validated['students'] as $studentData) {
            TeamMember::create([
                'group_id' => $group->id,
                'user_id'  => $studentData['user_id'],
                'role'     => $studentData['role'],
            ]);
        }

        return redirect()->route('teacher.page')->with('success', 'Team updated successfully!');
    }

    // ── UPDATE PASSWORD (shared) ─────────────────────────────────────
    public function updatePassword(Request $request)
    {
        $user = User::find(Auth::id());

        if (!$user) {
            return redirect()->route('login')->withErrors(['error' => 'You must be logged in.']);
        }

        $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('teacher.page')->with('success', 'Password updated successfully.');
    }






  





    /**
     * Full progress payload for the "View Progress" modal (student or teacher side).
     * AUTHORIZATION (teacher role): Adviser OR panelist. `is_adviser` in the JSON
     * response reflects TRUE adviser status and controls whether the front-end
     * renders the interactive attendance/remark-evaluation controls.
     */
    /**
 * Full progress payload for the "View Progress" modal (student or teacher side).
 * AUTHORIZATION (teacher role): Adviser OR panelist OR section-teacher.
 * Admin can view any group.
 */
public function getGroupProgress($groupId)
{
    $user  = Auth::user();
    $group = Group::with(['students', 'groupMilestones.milestone', 'team_members.student.user'])
        ->findOrFail($groupId);

    $isAdviser  = false;
    $isPanelist = false;

    if ($user->role === 'teacher') {
        $teacher = Teacher::where('user_id', $user->user_id)->first();
        if (!$teacher) {
            abort(403, 'Teacher profile not found.');
        }

        $isAdviserOfGroup  = (int) $group->adviser_id === (int) $teacher->id;

        $assignedRoomIds   = $teacher->evaluationRooms()
            ->pluck('evaluation_rooms.id')
            ->map(fn ($id) => (int) $id)
            ->toArray();
        $isPanelistOfGroup = $group->room_id && in_array((int) $group->room_id, $assignedRoomIds, true);

        $isSectionTeacher = false;
        if ($group->section_id) {
            $isSectionTeacher = \App\Models\Section::where('id', $group->section_id)
                ->where('user_id', $teacher->user_id)
                ->exists();
        }

        if (!$isAdviserOfGroup && !$isPanelistOfGroup && !$isSectionTeacher) {
            abort(403, 'You are not authorized to view the progress of this group.');
        }

        $isAdviser  = $isAdviserOfGroup;
        $isPanelist = $isPanelistOfGroup;
    }

    // ── Milestones for this group's capstone year ──
    $enabledStageIds = CapstoneStages::where('is_enabled', true)
        ->where('capstone_year_id', $group->capstone_year_id)
        ->pluck('id');

    $milestones = Milestone::whereIn('capstone_stage_id', $enabledStageIds)
        ->with(['capstoneStage', 'rubrics'])
        ->orderBy('step_order')
        ->get();

    $enabledMilestoneIds   = $milestones->pluck('id')->toArray();
    $completedMilestoneIds = $group->groupMilestones
        ->where('status', 'completed')
        ->whereIn('milestone_id', $enabledMilestoneIds)
        ->pluck('milestone_id')
        ->toArray();

    $overallProgress = $milestones->count()
        ? round((count($completedMilestoneIds) / $milestones->count()) * 100)
        : 0;

    $nextMilestone = $milestones->first(fn ($m) => !in_array($m->id, $completedMilestoneIds));

    // ──────────────────────────────────────────────────────────────
    // ✅ FIX: load EVERY evaluation for this group (no limit).
    // The view-progress modal groups evaluations by milestone, so
    // limiting to the 5 most recent hides evaluations on the other
    // milestones and makes them look "un-evaluated".
    // ──────────────────────────────────────────────────────────────
    $evaluations = Evaluation::where('group_id', $groupId)
        ->with(['milestone', 'teacher.user'])
        ->latest('evaluation_date')
        ->get()                       // ← no limit here
        ->map(function ($e) {
            $decoded = json_decode($e->feedback, true);
            if (is_array($decoded) && isset($decoded['feedback_text'])) {
                $feedbackText = $decoded['feedback_text'];
                $rubricScores = $decoded['rubric_scores'] ?? [];
            } else {
                $feedbackText = $e->feedback;
                $rubricScores = [];
            }

            $rubric = Rubric::where('milestone_id', $e->milestone_id)
                ->with('criteria')
                ->first();

            $criteriaData = [];
            if ($rubric) {
                foreach ($rubric->criteria as $criterion) {
                    $criteriaData[] = [
                        'criteria_name' => $criterion->criteria_name,
                        'weight'        => $criterion->weight,
                        'max_score'     => $criterion->max_score,
                        'given_score'   => $rubricScores[$criterion->id] ?? 0,
                    ];
                }
            }

            return [
                'milestone_id'    => $e->milestone_id,
                'milestone_title' => $e->milestone->milestone_title ?? 'Evaluation',
                'teacher_name'    => $e->teacher
                    ? ($e->teacher->teacher_first_name . ' ' . $e->teacher->teacher_last_name)
                    : 'Teacher',
                'evaluation_date' => $e->evaluation_date,
                'score'           => $e->score,
                'max_score'       => $e->max_score,
                'feedback'        => $feedbackText,
                'criteria'        => $criteriaData,
            ];
        });

    // ── Remarks & Absences (per milestone) ──
    $remarksByMilestone = \App\Models\Remarks::where('group_id', $groupId)
        ->get()
        ->keyBy('milestone_id');

    $allAbsences        = \App\Models\Absence::where('group_id', $groupId)->get();
    $absentStudentIds   = $allAbsences->pluck('user_id')->unique();
    $absentStudentsById = Student::whereIn('user_id', $absentStudentIds)->get()->keyBy('user_id');

    $groupMilestonesMap = $group->groupMilestones->keyBy('milestone_id');

    $milestoneData = $milestones->map(function ($m) use (
        $completedMilestoneIds, $nextMilestone, $remarksByMilestone,
        $allAbsences, $absentStudentsById, $groupMilestonesMap
    ) {
        $isCompleted = in_array($m->id, $completedMilestoneIds);
        $isNext      = $m->id === $nextMilestone?->id && !$isCompleted;

        $gm             = $groupMilestonesMap->get($m->id);
        $completionDate = $gm ? $gm->completion_date : null;

        $r           = $remarksByMilestone->get($m->id);
        $absentNames = $allAbsences->where('milestone_id', $m->id)->map(function ($a) use ($absentStudentsById) {
            $s = $absentStudentsById->get($a->user_id);
            return $s ? trim($s->student_first_name . ' ' . $s->student_last_name) : $a->user_id;
        })->values();

        return [
            'id'                   => $m->id,
            'title'                => $m->milestone_title,
            'description'          => $m->milestone_description,
            'start_date'           => $m->start_date,
            'due_date'             => $m->due_date,
            'step_order'           => $m->step_order,
            'is_completed'         => $isCompleted,
            'is_next'              => $isNext,
            'completion_date'      => $completionDate,
            'capstone_stage_id'    => $m->capstone_stage_id,
            'capstone_stage_title' => $m->capstoneStage->stage_title ?? 'Capstone',
            'has_rubric'           => $m->rubrics->isNotEmpty(),
            'remarks'              => $r ? [
                'all_present'      => (bool) $r->all_present,
                'compiled'         => (bool) $r->compiled,
                'deduction_points' => (int)  $r->deduction_points,
                'feedback'         => $r->feedback,
                'remarks_status'   => $r->remarks,
                'date_evaluated'   => $r->date_evaluated,
            ] : null,
            'absent_students'      => $absentNames,
        ];
    });

    return response()->json([
        'group_name'       => $group->group_name,
        'overall_progress' => $overallProgress,
        'milestones'       => $milestoneData,
        'next_milestone'   => $nextMilestone ? [
            'title'       => $nextMilestone->milestone_title,
            'description' => $nextMilestone->milestone_description,
            'start_date'  => $nextMilestone->start_date,
            'due_date'    => $nextMilestone->due_date,
        ] : null,
        'evaluations'      => $evaluations,
        'is_adviser'       => $isAdviser,
        'is_panelist'      => $isPanelist,
    ]);
}



/**
 * Teacher enters a room's join code to become its panelist.
 * Allows multiple panelists per room and multiple rooms per teacher.
 */
public function joinRoomWithCode(Request $request)
{
                
     Log::info('Join room request', ['code' => $request->join_code]);

    $teacher = Teacher::where('user_id', Auth::user()->user_id)->first();
    Log::info('Teacher', ['teacher' => $teacher]);

    $room = EvaluationRoom::where('join_code', strtoupper(trim($request->join_code)))->first();
    Log::info('Room', ['room' => $room]);

    $validated = $request->validate([
        'join_code' => 'required|string',
    ]);

    $teacher = Teacher::where('user_id', Auth::user()->user_id)->first();
    if (!$teacher) {
        if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
            return response()->json(['error' => 'Teacher profile not found.'], 404);
        }
        return redirect()->back()->with('error', 'Teacher profile not found.');
    }

    $room = EvaluationRoom::where('join_code', strtoupper(trim($validated['join_code'])))->first();
    if (!$room) {
        if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
            return response()->json(['error' => 'Invalid code. Please check with the admin and try again.'], 404);
        }
        return redirect()->back()->with('error', 'Invalid code. Please check with the admin and try again.');
    }

    $alreadyThisTeacher = $room->panelists()->where('teacher_id', $teacher->id)->exists();
    if ($alreadyThisTeacher) {
        if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'room_name' => $room->room_name, 'already_joined' => true]);
        }
        return redirect()->back()->with('error', "Already joined {$room->room_name}.");
    }

    // Check if teacher is already assigned to ANY room
    $isAlreadyAssigned = DB::table('room_panelists')->where('teacher_id', $teacher->id)->exists();
    if ($isAlreadyAssigned) {
        if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
            return response()->json(['error' => 'You are already assigned to an evaluation room.'], 422);
        }
        return redirect()->back()->with('error', 'You are already assigned to an evaluation room.');
    }

    // Add teacher as panelist to this room (avoiding duplicate entries)
    $room->panelists()->attach($teacher->id);

    if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
        return response()->json(['success' => true, 'room_name' => $room->room_name]);
    }

    return redirect()->back()->with('success', "Joined {$room->room_name} successfully!");
}






/**
 * Generate a unique serial number, prefixed by document type:
 * REC- for recommendation sheets, APR- for approval sheets, DOC- as a fallback.
 */
/**
 * Generate a unique, sequential serial number per document type and year:
 *   MCC-REC-2026-0001, MCC-REC-2026-0002, ...
 *   MCC-APR-2026-0001, MCC-APR-2026-0002, ...
 *   MCC-DOC-2026-0001, ... (fallback)
 */
private function generateSerialNumber(?string $documentType): string
{
    $prefix = match ($documentType) {
        'recommendation' => 'MCC-REC',
        'approval'       => 'MCC-APR',
        default          => 'MCC-DOC',
    };

    $year = now()->format('Y');

    // Grab the highest existing serial for this prefix + year
    $lastSerial = GroupCertificate::where('serial_number', 'like', "{$prefix}-{$year}-%")
        ->orderByDesc('serial_number')
        ->value('serial_number');

    $nextNumber = 1;
    if ($lastSerial && preg_match('/-(\d+)$/', $lastSerial, $m)) {
        $nextNumber = ((int) $m[1]) + 1;
    }

    // Walk forward until we find a free slot (protects against races / gaps)
    do {
        $serial = sprintf('%s-%s-%04d', $prefix, $year, $nextNumber);
        $nextNumber++;
    } while (GroupCertificate::where('serial_number', $serial)->exists());

    return $serial;
}


/**
 * Full printable certificate document for a group's earned certificate.
 * Shows capstone title and every team member's name.
 *
 * AUTHORIZATION: admin, the group's adviser, any panelist assigned to the
 * group's room, or a student who is a member of the group.
 */
public function showCertificate($groupId, $certificateId)
{
    $user = Auth::user();
    $group = Group::with(['team_members.student', 'adviser'])->findOrFail($groupId);

    $groupCertificate = GroupCertificate::where('group_id', $groupId)
        ->where('certificate_id', $certificateId)
        ->firstOrFail();

    $certificate = Certificate::findOrFail($certificateId);
    $milestone = Milestone::findOrFail($certificate->milestone_id);

    // ── Authorization ──
    $authorized = false;
    if ($user->role === 'admin') {
        $authorized = true;
    } elseif ($user->role === 'teacher') {
        $teacher = Teacher::where('user_id', $user->user_id)->first();
        if ($teacher) {
            $isAdviser = $group->adviser_id === $teacher->id;
            $assignedRoomIds = $teacher->evaluationRooms()->pluck('evaluation_rooms.id')->toArray();
            $isPanelist = in_array($group->room_id, $assignedRoomIds);
            $authorized = $isAdviser || $isPanelist;
        }
    } elseif ($user->role === 'student') {
        $authorized = $group->team_members->contains('user_id', $user->user_id);
    }

    if (!$authorized) {
        abort(403, 'You are not authorized to view this certificate.');
    }

    $members = $group->team_members->map(function ($tm) {
        return trim(
            (optional($tm->student)->student_first_name ?? '') . ' ' .
            (optional($tm->student)->student_last_name ?? '')
        );
    })->filter()->values()->all();

$adviserName = $group->adviser
    ? trim(
        $group->adviser->teacher_first_name . ' ' .
        ($group->adviser->teacher_middle_name ? substr($group->adviser->teacher_middle_name, 0, 1) . '.' : '') . ' ' .
        $group->adviser->teacher_last_name
    )
    : null;
    return view('certificates.print', [
        'certificate'  => $certificate,
        'group'        => $group,
        'milestone'    => $milestone,
        'members'      => $members,
        'adviserName'  => $adviserName,
        'issuedDate'   => $groupCertificate->issued_date,
    ]);
}

        /**
         * List certificates a group has earned (feeds the "Certificates" section
         * in the teacher/admin group progress modal).
         * AUTHORIZATION: same rule as showCertificate.
         */
        public function getGroupCertificates($groupId)
        {
            $user = Auth::user();
            $group = Group::findOrFail($groupId);

            $authorized = false;
            if ($user->role === 'admin') {
                $authorized = true;
            } elseif ($user->role === 'teacher') {
                $teacher = Teacher::where('user_id', $user->user_id)->first();
                if ($teacher) {
                    $isAdviser = $group->adviser_id === $teacher->id;
                    $assignedRoomIds = $teacher->evaluationRooms()->pluck('evaluation_rooms.id')->toArray();
                    $isPanelist = in_array($group->room_id, $assignedRoomIds);
                    $authorized = $isAdviser || $isPanelist;
                }
            } elseif ($user->role === 'student') {
                $authorized = Student::where('user_id', $user->user_id)
                    ->whereHas('groups', fn($q) => $q->where('groups.id', $groupId))
                    ->exists();
            }

            if (!$authorized) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $earned = GroupCertificate::where('group_id', $groupId)
                ->with('certificate')
                ->get()
                ->map(fn($gc) => [
                    'certificate_id'    => $gc->certificate_id,
                    'certificate_title' => $gc->certificate->certificate_title ?? 'Certificate',
                    'issued_date'       => $gc->issued_date,
                ]);

            return response()->json($earned);
        }


    // ── SHOW VERIFY EMAIL FORM ───────────────────────
    public function showVerifyEmailForm()
    {
        if (Auth::user()->email_verified_at !== null) {
            return $this->redirectByRole();
        }
        return view('verify-email');
    }

    // ── SEND CODE AFTER LOGIN ────────────────────────
    public function sendVerificationCodeAfterLogin(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'email' => 'required|email|unique:users,email,' . $user->id,
        ], [
            'email.unique' => 'This email is already taken.',
        ]);

        $email = $request->email;

        $code = (string) random_int(100000, 999999);
        Cache::put('verify_code_' . $user->user_id, $code, now()->addMinutes(10));

        $sent = Mailer::send(
            $email,
            $user->user_id,
            'Your Capstone Tracker verification code',
            "<p>Your verification code is:</p><h2>{$code}</h2><p>This code expires in 10 minutes.</p>"
        );

        if (!$sent) {
            return back()->withErrors(['email' => 'Failed to send verification email. Please try again.'])->withInput();
        }

        return back()->with('success', 'A verification code has been sent to your email.')
                      ->with('code_sent', true)
                      ->with('verified_email', $email);
    }

    // ── CONFIRM VERIFICATION CODE ────────────────────
    public function confirmVerificationCode(Request $request)
    {   
         /** @var \App\Models\User $user */
        $user = Auth::user();
        $request->validate([
            'email' => 'required|email|unique:users,email,' . $user->id,
            'code'  => 'required|string',
        ], [
            'email.unique' => 'This email is already taken.',
        ]);

        $cachedCode = Cache::get('verify_code_' . $user->user_id);

        if (!$cachedCode || $cachedCode !== $request->code) {
            return back()->withErrors(['code' => 'Invalid or expired verification code.'])
                         ->withInput()
                         ->with('code_sent', true)
                         ->with('verified_email', $request->email);
        }

        // ── PERSIST THE VERIFIED EMAIL ──
        $user->email = $request->email;
        $user->email_verified_at = now();
        $user->save();

        // ── UPDATE SYNCED PROFILE EMAIL ──
        switch ($user->role) {
            case 'student':
                Student::where('user_id', $user->user_id)->update(['student_email' => $request->email]);
                break;
            case 'teacher':
                Teacher::where('user_id', $user->user_id)->update(['teacher_email' => $request->email]);
                break;
            case 'admin':
                Admin::where('user_id', $user->user_id)->update(['admin_email' => $request->email]);
                break;
        }

        Cache::forget('verify_code_' . $user->user_id);

        return $this->redirectByRole();
    }

    // ── SHOW FORGOT PASSWORD FORM ───────────────────
    public function showForgotPasswordForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }
        return view('forgot-password');
    }

    // ── SEND FORGOT PASSWORD CODE ────────────────────
   // ── SEND FORGOT PASSWORD CODE ────────────────────
public function sendForgotPasswordCode(Request $request)
{
    $request->validate(['email' => 'required|email']);

    $email = $request->email;

    // First check the verified email on the users table
    $user = User::where('email', $email)->first();

    // Fallback: match against the profile-level email on file (student/teacher/admin)
    if (!$user) {
        $student = Student::where('student_email', $email)->first();
        $teacher = Teacher::where('teacher_email', $email)->first();
        $admin   = Admin::where('admin_email', $email)->first();

        $profileUserId = $student->user_id ?? $teacher->user_id ?? $admin->user_id ?? null;

        if ($profileUserId) {
            $user = User::where('user_id', $profileUserId)->first();
        }
    }

    if (!$user) {
        return back()->withErrors(['email' => 'No account was found with that email address.'])->withInput();
    }

    $code = (string) random_int(100000, 999999);
    Cache::put('reset_code_' . $user->user_id, $code, now()->addMinutes(10));

    $sent = Mailer::send(
        $email,
        $user->user_id,
        'Your Capstone Tracker password reset code',
        "<p>Your password reset verification code is:</p><h2>{$code}</h2><p>This code expires in 10 minutes.</p>"
    );

    if (!$sent) {
        return back()->withErrors(['email' => 'Failed to send password reset code. Please try again.'])->withInput();
    }

    return back()->with('success', "A password reset code has been sent to {$email}.")
                  ->with('reset_code_sent', true)
                  ->with('reset_user_id', $user->user_id)
                  ->with('reset_email', $email);
}

public function showResetConfirmation()
{
    // If no success flash, redirect to home (prevent direct access)
    if (!session('success')) {
        return redirect('/');
    }
    return view('reset-confirmation');
}
    // ── RESET PASSWORD WITH CODE ────────────────────
 public function resetPasswordWithCode(Request $request)
{
    $request->validate([
        'user_id'  => 'required|string',
        'code'     => 'required|string',
        'password' => 'required|min:6',
    ]);

    $user = User::where('user_id', $request->user_id)->first();
    if (!$user) {
        return back()->withErrors(['user_id' => 'User ID not found.'])
                     ->withInput()
                     ->with('reset_code_sent', true)
                     ->with('reset_user_id', $request->user_id);
    }

    $cachedCode = Cache::get('reset_code_' . $user->user_id);

    if (!$cachedCode || $cachedCode !== $request->code) {
        return back()->withErrors(['code' => 'Invalid or expired reset code.'])
                     ->withInput()
                     ->with('reset_code_sent', true)
                     ->with('reset_user_id', $request->user_id);
    }

    // Update password
    $user->password = bcrypt($request->password);
    $user->save();

    Cache::forget('reset_code_' . $user->user_id);

    // ✅ Redirect to confirmation page with a success flag
    return redirect()->route('password.reset.confirmation')
                     ->with('success', 'Your password has been reset successfully!');
}









   



    /**
     * Request revision for a group.
     */
/**
 * Request revision for a group.
 * Accepts structured revision data (chapters, IoT findings, additional objectives)
 * and stores it for later display.
 */
public function requestGroupRevision(Request $request, $groupId)
{

    
    $teacher = Teacher::where('user_id', Auth::user()->user_id)->firstOrFail();
    $group = Group::findOrFail($groupId);
        $alreadyRequested = \App\Models\Revision::where('group_id', $groupId)
                                                ->where('panelist_id', $teacher->id)
                                                ->exists();
        if ($alreadyRequested) {
            if ($request->ajax()) {
                return response()->json(['error' => 'You have already requested a revision for this group.'], 422);
            }
            return back()->with('error', 'You have already requested a revision for this group.');
        }
    $assignedRoomIds = $teacher->evaluationRooms()->pluck('evaluation_rooms.id')->toArray();
    if (!in_array($group->room_id, $assignedRoomIds)) {
        if ($request->ajax()) {
            return response()->json(['error' => 'You are not authorized to request revision for this group.'], 403);
        }
        return back()->with('error', 'You are not authorized to request revision for this group.');
    }

    $validated = $request->validate([
        'revision_description'      => 'required|string|max:2000',
        'chapters'                  => 'nullable|array',
        'chapters.*.chapter'        => 'required|string|max:255',
        'chapters.*.findings'       => 'required|string|max:1000',
        'chapters.*.remarks'        => 'nullable|string|max:1000',
        'iot_findings'               => 'nullable|array',
        'iot_findings.*.finding'    => 'required|string|max:1000',
        'iot_findings.*.remarks'    => 'nullable|string|max:1000',
        'additional_objectives'     => 'nullable|array',
        'additional_objectives.*'   => 'required|string|max:500',
    ]);

    DB::transaction(function () use ($validated, $teacher, $group) {
        $revision = \App\Models\Revision::create([
            'group_id'        => $group->id,
            'panelist_id'     => $teacher->id,
            'overall_remarks' => $validated['revision_description'],
        ]);

        foreach ($validated['chapters'] ?? [] as $ch) {
            $revision->documentation()->create([
                'chapter'  => $ch['chapter'],
                'findings' => $ch['findings'],
                'remarks'  => $ch['remarks'] ?? null,
            ]);
        }

        foreach ($validated['iot_findings'] ?? [] as $iot) {
            $revision->enhancements()->create([
                'enhancement' => $iot['finding'],
                'remarks'     => $iot['remarks'] ?? null,
            ]);
        }

        foreach ($validated['additional_objectives'] ?? [] as $obj) {
            $revision->objectives()->create([
                'objective' => $obj,
            ]);
        }

        $group->update([
            'revision_status'      => 'needs_revision',
            'revision_description' => $validated['revision_description'],
            'revision_id'          => $revision->id,
        ]);
    });

    if ($request->ajax()) {
        return response()->json(['success' => true, 'message' => 'Revision request submitted successfully!']);
    }
    return back()->with('success', 'Revision request submitted successfully!');
}

    /**
     * Mark a group as revised.
     */
    public function markGroupRevised(Request $request, $groupId)
    {
        $teacher = Teacher::where('user_id', Auth::user()->user_id)->firstOrFail();
        $group = Group::findOrFail($groupId);

        // Authorization check: Only the group's adviser (or a panelist) can mark it as revised.
        $assignedRoomIds = $teacher->evaluationRooms()->pluck('evaluation_rooms.id')->toArray();
        $isPanelist = in_array($group->room_id, $assignedRoomIds);
        $isAdviser = $group->adviser_id === $teacher->id;

        if (!$isAdviser && !$isPanelist) {
            if ($request->ajax()) {
                return response()->json(['error' => 'You are not authorized to mark this group as revised.'], 403);
            }
            return back()->with('error', 'You are not authorized to mark this group as revised.');
        }

        $group->update([
            'revision_status' => 'revised',
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Group successfully marked as revised.']);
        }
        return back()->with('success', 'Group successfully marked as revised.');
    }
   public function getRevisionDetails($groupId)
{
    $teacher = Teacher::where(
        'user_id',
        Auth::user()->user_id
    )->firstOrFail();

    $group = Group::findOrFail($groupId);

    // Must be a panelist assigned to this group's room
    $assignedRoomIds = $teacher->evaluationRooms()
        ->pluck('evaluation_rooms.id')
        ->toArray();

    if (!in_array($group->room_id, $assignedRoomIds)) {
        return response()->json([
            'error' => 'You are not authorized to verify this group.'
        ], 403);
    }

    /*
    |--------------------------------------------------------------------------
    | GET ONLY THIS TEACHER'S REVISION
    |--------------------------------------------------------------------------
    |
    | Teacher A -> Teacher A revision
    | Teacher B -> Teacher B revision
    |
    | Other revisions for this group DO NOT affect this query.
    |
    */

    $revision = \App\Models\Revision::with([
        'documentation',
        'enhancements',
        'objectives'
    ])
    ->where('group_id', $groupId)
    ->where('panelist_id', $teacher->id)
    ->first();

    if (!$revision) {
        return response()->json([
            'error' => 'You have not requested a revision for this group.'
        ], 404);
    }

    return response()->json([

        'revision_id' => $revision->id,

        'chapters' => $revision->documentation
            ->map(function ($item) {
                return [
                    'id'       => $item->id,
                    'chapter'  => $item->chapter,
                    'findings' => $item->findings,
                    'remarks'  => $item->remarks ?: 'Pending',
                ];
            })
            ->values(),

        'iot_findings' => $revision->enhancements
            ->map(function ($item) {
                return [
                    'id'      => $item->id,
                    'finding' => $item->enhancement,
                    'remarks' => $item->remarks ?: 'Pending',
                ];
            })
            ->values(),

        'additional_objectives' => $revision->objectives
            ->map(function ($item) {
                return [
                    'id'        => $item->id,
                    'objective' => $item->objective,
                    'remarks'   => $item->remarks ?: 'Pending',
                ];
            })
            ->values(),

        'overall_remarks' => $revision->overall_remarks,

        'approved_by' => $revision->approved_by ?? null,
    ]);
}

public function verifyRevision(Request $request, $groupId)
{
    $teacher = Teacher::where(
        'user_id',
        Auth::user()->user_id
    )->firstOrFail();

    $group = Group::findOrFail($groupId);


    // ==========================================
    // CHECK IF TEACHER IS A PANELIST
    // ==========================================

    $assignedRoomIds = $teacher->evaluationRooms()
        ->pluck('evaluation_rooms.id')
        ->toArray();

    if (!in_array($group->room_id, $assignedRoomIds)) {

        return response()->json([
            'success' => false,
            'error' => 'You are not authorized to verify this group.'
        ], 403);
    }


    // ==========================================
    // GET ONLY THIS TEACHER'S REVISION
    // ==========================================

    $revision = \App\Models\Revision::with([
        'documentation',
        'enhancements',
        'objectives'
    ])
    ->where('group_id', $groupId)
    ->where('panelist_id', $teacher->id)
    ->first();


    if (!$revision) {

        return response()->json([
            'success' => false,
            'error' => 'You have not requested a revision for this group.'
        ], 404);
    }


    // ==========================================
    // VALIDATION
    // ==========================================

    $validated = $request->validate([

        'chapters' => 'nullable|array',

        'chapters.*.chapter' =>
            'nullable|string',

        'chapters.*.findings' =>
            'nullable|string',

        'chapters.*.completed' =>
            'nullable|boolean',

        'chapters.*.remarks' =>
            'nullable|string',


        'iot' => 'nullable|array',

        'iot.*.finding' =>
            'nullable|string',

        'iot.*.completed' =>
            'nullable|boolean',

        'iot.*.remarks' =>
            'nullable|string',


        'objectives' => 'nullable|array',

        'objectives.*.objective' =>
            'nullable|string',

        'objectives.*.completed' =>
            'nullable|boolean',

        'objectives.*.remarks' =>
            'nullable|string',

    ]);


    try {

        DB::transaction(function () use (
            $revision,
            $validated
        ) {


            // =====================================
            // CHAPTERS
            // =====================================

            foreach (
                $validated['chapters'] ?? []
                as $chapter
            ) {

                if (
                    empty($chapter['chapter']) ||
                    !isset($chapter['findings'])
                ) {
                    continue;
                }


                $status =
                    !empty($chapter['completed'])
                        ? 'Completed'
                        : 'Pending';


                $revision->documentation()
                    ->where(
                        'chapter',
                        $chapter['chapter']
                    )
                    ->where(
                        'findings',
                        $chapter['findings']
                    )
                    ->update([
                        'remarks' => $status
                    ]);
            }



            // =====================================
            // SYSTEM / IoT
            // =====================================

            foreach (
                $validated['iot'] ?? []
                as $iot
            ) {

                if (empty($iot['finding'])) {
                    continue;
                }


                $status =
                    !empty($iot['completed'])
                        ? 'Completed'
                        : 'Pending';


                $revision->enhancements()
                    ->where(
                        'enhancement',
                        $iot['finding']
                    )
                    ->update([
                        'remarks' => $status
                    ]);
            }



            // =====================================
            // ADDITIONAL OBJECTIVES
            // =====================================

            foreach (
                $validated['objectives'] ?? []
                as $objective
            ) {

                if (empty($objective['objective'])) {
                    continue;
                }


                $status =
                    !empty($objective['completed'])
                        ? 'Completed'
                        : 'Pending';


                $revision->objectives()
                    ->where(
                        'objective',
                        $objective['objective']
                    )
                    ->update([
                        'remarks' => $status
                    ]);
            }

        });


        return response()->json([

            'success' => true,

            'revision_id' => $revision->id,

            'message' =>
                'Revision verification submitted successfully!'

        ]);

    } catch (\Throwable $e) {

        Log::error(
            'Revision verification failed',
            [
                'group_id' => $groupId,
                'teacher_id' => $teacher->id,
                'revision_id' => $revision->id,
                'error' => $e->getMessage()
            ]
        );


        return response()->json([

            'success' => false,

            'error' =>
                'Verification could not be saved.',

            // Remove this after debugging if desired.
            'debug' => $e->getMessage()

        ], 500);
    }
}
public function getStudentGroup($groupId)
{
    $user = Auth::user();

    if (!$user || $user->role !== 'student') {
        return response()->json([
            'error' => 'Unauthorized.'
        ], 403);
    }


    $student = Student::where(
        'user_id',
        $user->user_id
    )->firstOrFail();


    $group = Group::with([
        'team_members.student.user'
    ])
    ->findOrFail($groupId);


    // Make sure this student belongs to this group
    $belongsToGroup = $group->team_members
        ->contains('user_id', $student->user_id);


    if (!$belongsToGroup) {

        return response()->json([
            'error' => 'You are not a member of this group.'
        ], 403);

    }


    return response()->json([

        'id' => $group->id,

        'group_name' => $group->group_name,

        'capstone_title' => $group->capstone_title,

        'members' => $group->team_members
            ->map(function ($member) {

                $student = $member->student;

                return [

                    'user_id' => $member->user_id,

                    'name' => $student
                        ? trim(
                            $student->student_first_name . ' ' .
                            $student->student_last_name
                        )
                        : $member->user_id

                ];

            })
            ->values()

    ]);
}
public function getStudentRevision($groupId)
{
    $user = Auth::user();

    if (!$user || $user->role !== 'student') {

        return response()->json([
            'error' => 'Unauthorized.'
        ], 403);

    }


    $student = Student::where(
        'user_id',
        $user->user_id
    )->firstOrFail();


    $group = Group::with([
        'team_members'
    ])
    ->findOrFail($groupId);


    // Student must belong to this group
    $belongsToGroup = $group->team_members
        ->contains('user_id', $student->user_id);


    if (!$belongsToGroup) {

        return response()->json([
            'error' => 'You are not a member of this group.'
        ], 403);

    }


    // =====================================
    // GET CURRENT REVISION
    // =====================================

    $revision = null;


    if ($group->revision_id) {

        $revision = \App\Models\Revision::with([
            'documentation',
            'enhancements',
            'objectives'
        ])
        ->where('id', $group->revision_id)
        ->where('group_id', $groupId)
        ->first();

    }


    // Fallback
    if (!$revision) {

        $revision = \App\Models\Revision::with([
            'documentation',
            'enhancements',
            'objectives'
        ])
        ->where('group_id', $groupId)
        ->latest('id')
        ->first();

    }


    if (!$revision) {

        return response()->json([

            'chapters' => [],

            'iot_findings' => [],

            'additional_objectives' => [],

            'overall_remarks' => null,

            'approved_by' => null,

        ]);

    }


    return response()->json([

        'revision_id' => $revision->id,


        // =====================================
        // CHAPTER FINDINGS
        // =====================================

        'chapters' => $revision->documentation
            ->map(function ($item) {

                return [

                    'id' => $item->id,

                    'chapter' => $item->chapter,

                    'findings' => $item->findings,

                    'remarks' => $item->remarks ?: 'Pending',

                ];

            })
            ->values(),


        // =====================================
        // SYSTEM / IoT
        // =====================================

        'iot_findings' => $revision->enhancements
            ->map(function ($item) {

                return [

                    'id' => $item->id,

                    'finding' => $item->enhancement,

                    'remarks' => $item->remarks ?: 'Pending',

                ];

            })
            ->values(),


        // =====================================
        // ADDITIONAL OBJECTIVES
        // =====================================

        'additional_objectives' => $revision->objectives
            ->map(function ($item) {

                return [

                    'id' => $item->id,

                    'objective' => $item->objective,

                    'remarks' => $item->remarks ?: 'Pending',

                ];

            })
            ->values(),


        'overall_remarks' =>
            $revision->overall_remarks,


        'approved_by' =>
            $revision->approved_by ?? null,

    ]);
}
public function getStudentRevisionById($groupId, $revisionId)
{
    $user = Auth::user();
    if (!$user || $user->role !== 'student') {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $student = Student::where('user_id', $user->user_id)->firstOrFail();
    $group = Group::with('team_members')->findOrFail($groupId);

    // Ensure student belongs to this group
    if (!$group->team_members->contains('user_id', $student->user_id)) {
        return response()->json(['error' => 'You are not a member of this group.'], 403);
    }

    $revision = \App\Models\Revision::with([
        'documentation',
        'enhancements',
        'objectives'
    ])->where('group_id', $groupId)
      ->where('id', $revisionId)
      ->first();

    if (!$revision) {
        return response()->json(['error' => 'Revision not found.'], 404);
    }

    return response()->json([
        'chapters' => $revision->documentation->map(fn($d) => [
            'chapter'  => $d->chapter,
            'findings' => $d->findings,
            'remarks'  => $d->remarks ?: 'Pending',
        ]),
        'iot_findings' => $revision->enhancements->map(fn($e) => [
            'finding' => $e->enhancement,
            'remarks' => $e->remarks ?: 'Pending',
        ]),
        'additional_objectives' => $revision->objectives->map(fn($o) => [
            'objective' => $o->objective,
            'remarks'   => $o->remarks ?: 'Pending',
        ]),
        'overall_remarks' => $revision->overall_remarks,
        'approved_by'     => $revision->approved_by,
    ]);
}

public function getApprovalSheet($groupId)
{
    $user = Auth::user();
    $student = Student::where('user_id', $user->user_id)->firstOrFail();

    $group = Group::with(['team_members.student', 'adviser', 'room.panelists'])
        ->findOrFail($groupId);

    if (!$group->team_members->contains('user_id', $user->user_id)) {
        return response()->json(['error' => 'You are not a member of this group.'], 403);
    }

    // Members
    $members = $group->team_members->map(function ($tm) {
        $s = $tm->student;
        return trim(($s->student_first_name ?? '') . ' ' . ($s->student_last_name ?? ''));
    })->filter()->values()->all();

    // Adviser
    $adviser = $group->adviser
        ? trim(($group->adviser->teacher_first_name ?? '') . ' ' . ($group->adviser->teacher_last_name ?? ''))
        : null;

    // Panelists – get from room, include pivot data if available
    $panelists = [];
    if ($group->room) {
        foreach ($group->room->panelists as $p) {
            $pivot = $p->pivot; // the room_panelists pivot
            $panelists[] = [
                'name' => trim(($p->teacher_first_name ?? '') . ' ' . ($p->teacher_last_name ?? '')),
                'role' => $pivot->role ?? 'Member',   // if you have a role column, else 'Member'
                'date' => $pivot->date ?? null,        // if you have a date column
            ];
        }
    }

    // If no panelists, provide a fallback (optional)
    if (empty($panelists)) {
        // You could add a placeholder chairman from the department
        $panelists[] = [
            'name' => 'DINO L. HUSTRIJIMO, MIT',
            'role' => 'Chairman, Board of Panels',
            'date' => null,
        ];
    }

    // Oral exam result and date – fetch from the "Oral Presentation" milestone evaluation
    $oralExamResult = '—';
    $oralExamDate = null;

    $oralMilestone = Milestone::where('milestone_title', 'like', '%Oral Presentation%')->first();
    if ($oralMilestone) {
        $eval = Evaluation::where('group_id', $groupId)
            ->where('milestone_id', $oralMilestone->id)
            ->latest('evaluation_date')
            ->first();
        if ($eval) {
            $oralExamResult = $eval->score >= ($eval->max_score * 0.6) ? 'PASSED' : 'FAILED'; // threshold
            $oralExamDate = $eval->evaluation_date ? \Carbon\Carbon::parse($eval->evaluation_date)->format('F d, Y') : null;
        }
    }

    // School President – you can store in settings or hardcode
    $president = 'DR. FLORIPIS A. MONTECILLO, Ed.D.';

  $approvalRecord = GroupCertificate::where('group_id', $groupId)
    ->where(function ($q) {
        $q->whereHas('certificate', function ($c) {
            $c->where('document_type', 'approval')
              ->orWhere('certificate_title', 'like', '%Approval%');
        })
        ->orWhere('serial_number', 'like', 'MCC-APR-%');
    })
    ->latest('issued_date')
    ->first();

    return response()->json([
        'capstone_title'     => $group->capstone_title,
        'members'            => $members,
        'adviser'            => $adviser,
        'panelists'          => $panelists,
        'oral_exam_result'   => $oralExamResult,
        'oral_exam_date'     => $oralExamDate,
        'school_president'   => $president,
        'serial_number'      => $approvalRecord?->serial_number,    
    ]);
}


/**
 * All revision sheets (from every panelist) for a group — teacher-facing.
 */
public function getAllRevisionsForGroup($groupId)
{
    $group = Group::findOrFail($groupId);

    $revisions = \App\Models\Revision::with(['documentation', 'enhancements', 'objectives', 'panelist'])
        ->where('group_id', $groupId)
        ->orderByDesc('created_at')
        ->get()
        ->map(function ($rev) {
            return [
                'id'           => $rev->id,
                'panelist_name' => $rev->panelist
                    ? $rev->panelist->teacher_first_name . ' ' . $rev->panelist->teacher_last_name
                    : 'Panelist',
                'created_at'   => $rev->created_at,
                'overall_remarks' => $rev->overall_remarks,
                'chapters' => $rev->documentation->map(fn($d) => [
                    'chapter'  => $d->chapter,
                    'findings' => $d->findings,
                    'remarks'  => $d->remarks ?: 'Pending',
                ]),
                'iot_findings' => $rev->enhancements->map(fn($e) => [
                    'finding' => $e->enhancement,
                    'remarks' => $e->remarks ?: 'Pending',
                ]),
                'additional_objectives' => $rev->objectives->map(fn($o) => [
                    'objective' => $o->objective,
                    'remarks'   => $o->remarks ?: 'Pending',
                ]),
            ];
        });

    return response()->json([
        'group_name' => $group->group_name,
        'revisions'  => $revisions,
    ]);
}
public function getRecommendationSheet($groupId)
{
    $user = Auth::user();
    $student = Student::where('user_id', $user->user_id)->firstOrFail();

    $group = Group::with(['team_members.student', 'adviser'])
        ->findOrFail($groupId);

    if (!$group->team_members->contains('user_id', $user->user_id)) {
        return response()->json(['error' => 'You are not a member of this group.'], 403);
    }

    // Members
    $members = $group->team_members->map(function ($tm) {
        $s = $tm->student;
        return trim(($s->student_first_name ?? '') . ' ' . ($s->student_last_name ?? ''));
    })->filter()->values()->all();

    // Adviser (full name)
    $adviser = $group->adviser
        ? trim(($group->adviser->teacher_first_name ?? '') . ' ' . ($group->adviser->teacher_last_name ?? ''))
        : null;

    // Date issued – we can use the current date or a stored date (adjust as needed)
    $dateIssued = now()->format('Y-m-d');
$recRecord = GroupCertificate::where('group_id', $groupId)
    ->whereHas('certificate', fn($q) =>
        $q->where('document_type', 'recommendation')
          ->orWhere('certificate_title', 'like', '%Recommendation%'))
    ->latest('issued_date')
    ->first();
    return response()->json([
        'capstone_title' => $group->capstone_title,
        'members'        => $members,
        'adviser'        => $adviser,
        'date_issued'    => $dateIssued,
        'group_name'     => $group->group_name,
          'serial_number'  => $recRecord?->serial_number, 
    ]);
}


/**
 * Issue the recommendation sheet for a group at a given milestone.
 */
public function issueRecommendationSheet(Request $request)
{
    $validated = $request->validate([
        'group_id'     => 'required|exists:groups,id',
        'milestone_id' => 'required|exists:milestones,id',
    ]);

    // Locate the recommendation certificate for this milestone …
    $certificate = Certificate::where('milestone_id', $validated['milestone_id'])
        ->where(function ($q) {
            $q->where('document_type', 'recommendation')
              ->orWhere('certificate_title', 'like', '%Recommendation%');
        })
        ->first();

    // … else any certificate on the milestone …
    if (! $certificate) {
        $certificate = Certificate::where('milestone_id', $validated['milestone_id'])->first();
    }

    // … else auto-create one (e.g. Capstone 2 has no seeded certificate yet).
    if (! $certificate) {
        $certificate = Certificate::create([
            'certificate_title'       => 'Recommendation Sheet',
            'document_type'           => 'recommendation',
            'certificate_description' => 'partial fulfillment of the requirements for the degree of '
                . 'Bachelor of Science in Information Technology has been examined, '
                . 'accepted, and recommended for Oral Presentation.',
            'milestone_id'            => $validated['milestone_id'],
            'is_locked'               => 1,
        ]);
    }

    $already = GroupCertificate::where('group_id', $validated['group_id'])
        ->where('certificate_id', $certificate->id)
        ->first();

    if ($already) {
        return response()->json([
            'success'     => true,
            'already'     => true,
            'message'     => 'Recommendation sheet was already issued on '
                             . \Carbon\Carbon::parse($already->issued_date)->format('M d, Y') . '.',
            'issued_date' => $already->issued_date,
        ]);
    }

    $issued = GroupCertificate::create([
        'group_id'       => $validated['group_id'],
        'certificate_id' => $certificate->id,
        'issued_date'    => now()->toDateString(),
        'serial_number'  => $this->generateSerialNumber('recommendation'),
    ]);

    return response()->json([
        'success'     => true,
        'message'     => 'Recommendation sheet issued to the group.',
        'issued_date' => $issued->issued_date,
    ]);
}

/**
 * Lightweight status check used by the view modal.
 */
public function getRecommendationStatus($groupId)
{
    $record = GroupCertificate::with('certificate')
        ->where('group_id', $groupId)
        ->whereHas('certificate', function ($q) {
            $q->where('document_type', 'recommendation')
              ->orWhere('certificate_title', 'like', '%Recommendation%');
        })
        ->first();

    return response()->json([
        'issued'         => (bool) $record,
        'issued_date'    => $record?->issued_date,
        'certificate_id' => $record?->certificate_id,
    ]);
}

/**
 * Issue a document (recommendation / approval / revision) to a group.
 */
public function issueSheet(Request $request)
{
    $validated = $request->validate([
        'group_id'      => 'required|exists:groups,id',
        'milestone_id'  => 'nullable|exists:milestones,id',
        'document_type' => 'required|in:recommendation,approval,revision',
    ]);

    $titleMap = [
        'recommendation' => 'Recommendation Sheet',
        'approval'       => 'Approval Sheet',
        'revision'       => 'Revision Sheet',
    ];

    $type  = $validated['document_type'];
    $title = $titleMap[$type];

    // Find or create the matching certificate
    $certificate = Certificate::query()
        ->where('document_type', $type)
        ->when(
            ! empty($validated['milestone_id']),
            fn ($q) => $q->where('milestone_id', $validated['milestone_id']),
            fn ($q) => $q->whereNull('milestone_id')
        )
        ->first();

    if (! $certificate) {
        $certificate = Certificate::create([
            'certificate_title'       => $title,
            'document_type'           => $type,
            'certificate_description' => $type === 'revision'
                ? 'Official revision sheet issued by the panel.'
                : 'partial fulfillment of the requirements for the degree of '
                  . 'Bachelor of Science in Information Technology has been examined, '
                  . 'accepted, and recommended for Oral Presentation.',
            'milestone_id'            => $validated['milestone_id'] ?? null,
            'is_locked'               => 1,
        ]);
    }

    // Idempotent — don't double-issue
    $already = GroupCertificate::where('group_id', $validated['group_id'])
        ->where('certificate_id', $certificate->id)
        ->first();

    if ($already) {
        return response()->json([
            'success'       => true,
            'already'       => true,
            'message'       => "{$title} was already issued on "
                               . Carbon::parse($already->issued_date)->format('M d, Y') . '.',
            'issued_date'   => $already->issued_date,
            'serial_number' => $already->serial_number,
        ]);
    }

    $issued = GroupCertificate::create([
        'group_id'       => $validated['group_id'],
        'certificate_id' => $certificate->id,
        'issued_date'    => now()->toDateString(),
           'serial_number'  => $this->generateSerialNumber($type),
    ]);

    return response()->json([
        'success'       => true,
        'message'       => "{$title} issued successfully.",
        'issued_date'   => $issued->issued_date,
        'serial_number' => $issued->serial_number,
    ]);
}
/**
 * Status check used by the view modal.
 */
public function getSheetStatus(Request $request, $groupId)
{
    $type = $request->query('type', 'recommendation');
    if (! in_array($type, ['recommendation', 'approval', 'revision'], true)) {
        $type = 'recommendation';
    }

    $prefixMap = [
        'recommendation' => 'MCC-REC-%',
        'approval'       => 'MCC-APR-%',
        'revision'       => 'MCC-DOC-%',   // or whatever revision uses
    ];
    $titleMap = [
        'recommendation' => '%Recommendation%',
        'approval'       => '%Approval%',
        'revision'       => '%Revision%',
    ];

    $record = GroupCertificate::with('certificate')
        ->where('group_id', $groupId)
        ->where(function ($q) use ($type, $prefixMap, $titleMap) {
            $q->whereHas('certificate', fn ($c) => $c->where('document_type', $type))
              ->orWhereHas('certificate', fn ($c) => $c->where('certificate_title', 'like', $titleMap[$type]))
              ->orWhere('serial_number', 'like', $prefixMap[$type]);
        })
        ->first();

    return response()->json([
        'issued'         => (bool) $record,
        'issued_date'    => $record?->issued_date,
        'serial_number'  => $record?->serial_number,
        'certificate_id' => $record?->certificate_id,
    ]);
}
}