<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Unique;
use App\Models\Evaluation;
use App\Models\Certificate;
use App\Models\GroupCertificate;
use App\Models\GroupMilestones;
use App\Support\DefaultRubrics; 
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

 class admin_controller extends Controller
{
        // ── ADD TEACHER (admin) ──────────────────────────────────────────
    public function addTeacher(Request $request)
    {
        $validatedData = $request->validate([
            'teacher_id' => 'required|unique:users,user_id',
            'teacher_first_name' => 'required|string|max:255',
           'teacher_middle_name' => 'nullable|string|max:255',
            'teacher_last_name' => 'required|string|max:255',
        ]);

        User::create([
            'user_id' => $validatedData['teacher_id'],
            'role'    => 'teacher',
        ]);

        Teacher::create([
            'user_id'             => $validatedData['teacher_id'],
            'teacher_first_name'  => $validatedData['teacher_first_name'],
            'teacher_middle_name' => $validatedData['teacher_middle_name'],
            'teacher_last_name'   => $validatedData['teacher_last_name'],
        ]);

        return redirect()->route('admin.page')->with('success', 'Teacher added successfully.');
    }
        // ── TEACHER EDIT (admin) ──────────────────────────────────────
    public function editTeacher(Request $request)
    {
        $validated = $request->validate([
            'original_teacher_id' => 'required|string|exists:teachers,user_id',
            'teacher_id'           => 'required|string',
            'teacher_first_name'   => 'required|string|max:255',
            'teacher_middle_name'  => 'nullable|string|max:255',
            'teacher_last_name'    => 'required|string|max:255',
            'teacher_email'        => 'required|email',
            'contact_number'       => 'nullable',
        ]);

        $teacher = Teacher::where('user_id', $validated['original_teacher_id'])->firstOrFail();

        $validated['teacher_email'] = $request->validate([
            'teacher_email' => 'required|email|unique:teachers,teacher_email,' . $teacher->id,
        ])['teacher_email'];

        $teacher->update([
            'teacher_first_name'  => $validated['teacher_first_name'],
            'teacher_middle_name' => $validated['teacher_middle_name'],
            'teacher_last_name'   => $validated['teacher_last_name'],
            'teacher_email'       => $validated['teacher_email'],
            'contact_number'      => $validated['contact_number'] ?? '',
        ]);

        return back()->with('success', 'Teacher updated successfully.');
    }
    // techer delete
        public function deleteTeacher(Request $request)
    {
        $validated = $request->validate([
            'teacher_id'      => 'required|string|exists:teachers,user_id',
            'admin_password'  => 'required|string',
        ]);

        if (!Hash::check($validated['admin_password'], Auth::user()->password)) {
            return back()
                ->withErrors(['admin_password' => 'Incorrect password. Teacher was not deleted.'])
                ->withInput();
        }

        $teacher = Teacher::where('user_id', $validated['teacher_id'])->firstOrFail();

        // Block deletion if they're still advising groups — force reassignment first
        $activeGroups = Group::where('adviser_id', $teacher->id)->count();
        if ($activeGroups > 0) {
            return back()->withErrors([
                'teacher_id' => "Cannot delete: this teacher is still adviser for {$activeGroups} group(s). Reassign those groups first."
            ]);
        }

        DB::transaction(function () use ($teacher, $validated) {
            // Free up any sections assigned to this teacher
            Section::where('user_id', $teacher->user_id)->update(['user_id' => null]);

            $teacher->delete();
            User::where('user_id', $validated['teacher_id'])->delete();
        });

        return back()->with('success', 'Teacher deleted successfully.');
    }


//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    // ── ADD STUDENT (admin) ──────────────────────────────────────────
    public function addStudent(Request $request)
    {
        $validatedData = $request->validate([
            'student_id'          => 'required|unique:users,user_id',
            'student_first_name'  => 'required|string|max:255',
            'student_middle_name' => 'nullable|string|max:255',
            'student_last_name'   => 'required|string|max:255',
            'course'              => 'required|string|max:255',
            'section'             => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($validatedData) {
            User::create([
                'user_id' => $validatedData['student_id'],
                'role'    => 'student',
            ]);

            Student::create([
                'user_id'            => $validatedData['student_id'],
                'student_first_name' => $validatedData['student_first_name'],
                'student_middle_name'=> $validatedData['student_middle_name'] ?? null,
                'student_last_name'  => $validatedData['student_last_name'],
                'student_email'      => null,
                'contact_number'     => null,
                'course'             => $validatedData['course'],
                'section'            => $validatedData['section'],
            ]);
        });

        return redirect()->route('admin.page')->with('success', 'Student added.');
    }
        // ── STUDENT EDIT (admin) ─────────────────────────────────────────
    public function editStudent(Request $request)
    {
        $validated = $request->validate([
            'original_student_id' => 'required|string|exists:students,user_id',
            'student_id' => 'required|string',
            'student_first_name' => 'required|string',
            'student_middle_name' => 'nullable|string',
            'student_last_name' => 'required|string',
            'student_email' => 'required|email',
            'contact_number' => 'required|string|size:11',
            'section' => 'required|string',
        ]);

        $student = Student::where('user_id', $validated['original_student_id'])->firstOrFail();

        $student->update([
            'student_first_name' => $validated['student_first_name'],
            'student_middle_name' => $validated['student_middle_name'],
            'student_last_name' => $validated['student_last_name'],
            'student_email' => $validated['student_email'],
            'contact_number' => $validated['contact_number'],
            'section' => $validated['section'],
        ]);

        return back()->with('success', 'Student updated successfully.');
    }
    // delete student
    public function deleteStudent(Request $request)
    {
            $validated = $request->validate([
                'student_id'     => 'required|string|exists:students,user_id',
                'admin_password' => 'required|string',
            ]);

            $adminUser = Auth::user();

            if (!Hash::check($validated['admin_password'], $adminUser->password)) {
                return back()
                    ->withErrors(['admin_password' => 'Incorrect password. Student was not deleted.'])
                    ->withInput();
            }

            DB::transaction(function () use ($validated) {
                TeamMember::where('user_id', $validated['student_id'])->delete();
                Evaluation::where('student_id', $validated['student_id'])->delete();
                Student::where('user_id', $validated['student_id'])->delete();
                User::where('user_id', $validated['student_id'])->delete();
            });

            return back()->with('success', 'Student deleted successfully.');
    }


//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------



    // ── ADD RUBRIC (admin) ──────────────────────────────────────────
    public function addRubric(Request $request)
    {
        $validatedData = $request->validate([
            'rubric_name'     => 'required|string|max:255',
            'milestone_id'    => 'nullable|exists:milestones,id',
            'criteria_name'   => 'required|array|min:1',
            'criteria_name.*' => 'required|string|max:255',
            'weight'          => 'required|array|min:1',
            'weight.*'        => 'required|numeric|min:0|max:100',
            'score'           => 'required|array|min:1',
            'score.*'         => 'required|numeric|min:0',
        ]);

        if (round(array_sum($validatedData['weight']), 2) != 100) {
            return back()
                ->withErrors(['weight' => 'Criteria weights must add up to 100%.'])
                ->withInput();
        }

        $rubric = Rubric::create([
            'rubric_name'  => $validatedData['rubric_name'],
            'milestone_id' => $validatedData['milestone_id'],
        ]);

        foreach ($validatedData['criteria_name'] as $i => $name) {
            $rubric->criteria()->create([
                'criteria_name' => $name,
                'weight'        => $validatedData['weight'][$i],
                'max_score'     => $validatedData['score'][$i],
            ]);
        }

        return redirect()->route('admin.page')->with('success', 'Rubric created successfully.');
    }


      // ── RUBRIC GET/UPDATE/DELETE (admin) ────────────────────────────
    public function getRubric($id)
    {
        $rubric = Rubric::with('criteria')->findOrFail($id);

        return response()->json([
            'id' => $rubric->id,
            'rubric_name' => $rubric->rubric_name,
            'capstone_id' => $rubric->milestone->capstone_stage_id ?? null,
            'milestone_id' => $rubric->milestone_id,
            'criteria' => $rubric->criteria->map(function ($c) {
                return [
                    'criteria_name' => $c->criteria_name,
                    'weight' => $c->weight,
                    'max_score' => $c->max_score,
                ];
            }),
        ]);
    }

    // update rubrics
        public function updateRubric(Request $request, $id)
    {
        $validated = $request->validate([
            'rubric_name' => 'required|string|max:255',
            'capstone_id' => 'required|exists:capstone_stages,id',
            'milestone_id' => 'required|exists:milestones,id',
            'criteria_name' => 'required|array|min:1',
            'criteria_name.*' => 'required|string|max:255',
            'weight' => 'required|array|min:1',
            'weight.*' => 'required|numeric|min:0|max:100',
            'score' => 'required|array|min:1',
            'score.*' => 'required|numeric|min:0',
        ]);

        $totalWeight = array_sum($validated['weight']);
        if (round($totalWeight, 2) != 100) {
            return back()->withErrors(['weight' => 'Total weight must equal 100%.'])->withInput();
        }

        $rubric = Rubric::findOrFail($id);
        $rubric->update([
            'rubric_name' => $validated['rubric_name'],
            'milestone_id' => $validated['milestone_id'],
        ]);

        $rubric->criteria()->delete();
        foreach ($validated['criteria_name'] as $i => $name) {
            $rubric->criteria()->create([
                'criteria_name' => $name,
                'weight' => $validated['weight'][$i],
                'max_score' => $validated['score'][$i],
            ]);
        }
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Rubric updated successfully.'
            ]);
        }

            return redirect()->route('admin.page')->with('success', 'Rubric updated successfully.');
    }

    // delte rubrics
    public function deleteRubrics(Request $request)
    {
        $validated = $request->validate([
            'rubric_id' => 'required|exists:rubrics,id',
            'admin_password' => 'required|string',
        ]);

        if (!Hash::check($validated['admin_password'], Auth::user()->password)) {
            return back()->withErrors(['admin_password' => 'Incorrect password. Rubric was not deleted.'])->withInput();
        }

        $rubric = Rubric::findOrFail($validated['rubric_id']);
        $rubric->criteria()->delete();
        $rubric->delete();

        return back()->with('success', 'Rubric deleted successfully.');
    }



//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


    // ── ADD MILESTONE (admin) ──────────────────────────────────────
    public function addMilestone(Request $request)
    {
        $validatedData = $request->validate([
            'milestone_title' => 'required|string|max:255',
            'capstone_stage'  => 'required|integer|exists:capstone_stages,id',
            'order'           => [
                'required',
                'integer',
                Rule::unique('milestones', 'step_order')
                    ->where('capstone_stage_id', $request->input('capstone_stage'))
            ],
            'description'     => 'nullable|string|max:255',
            'due_date'        => 'required|date',
            'start_date'      => 'required|date',
            
            // Optional Rubric fields
            'add_rubric'      => 'nullable|string',
            'rubric_name'     => 'nullable|required_if:add_rubric,on|string|max:255',
            'criteria_name'   => 'nullable|required_if:add_rubric,on|array',
            'criteria_name.*' => 'nullable|required_if:add_rubric,on|string|max:255',
            'weight'          => 'nullable|required_if:add_rubric,on|array',
            'weight.*'        => 'nullable|required_if:add_rubric,on|numeric|min:0|max:100',
            'score'           => 'nullable|required_if:add_rubric,on|array',
            'score.*'         => 'nullable|required_if:add_rubric,on|numeric|min:0',
        ]);

        if ($request->has('add_rubric') && $request->add_rubric === 'on') {
            if (round(array_sum($validatedData['weight'] ?? []), 2) != 100) {
                return back()
                    ->withErrors(['weight' => 'Criteria weights must add up to 100%.'])
                    ->withInput()
                    ->with('add_milestone', true);
            }
        }

        $milestone = Milestone::create([
            'milestone_title'       => $validatedData['milestone_title'],
            'step_order'            => $validatedData['order'],
            'milestone_description' => $validatedData['description'] ?? '',
            'start_date'            => $validatedData['start_date'],
            'due_date'              => $validatedData['due_date'],
            'capstone_stage_id'     => $validatedData['capstone_stage']
        ]);

        if ($request->has('add_rubric') && $request->add_rubric === 'on') {
            $rubric = Rubric::create([
                'rubric_name'  => $validatedData['rubric_name'] ?: ($validatedData['milestone_title'] . ' Rubric'),
                'milestone_id' => $milestone->id,
            ]);

            if (!empty($validatedData['criteria_name'])) {
                foreach ($validatedData['criteria_name'] as $i => $name) {
                    if ($name) {
                        $rubric->criteria()->create([
                            'criteria_name' => $name,
                            'weight'        => $validatedData['weight'][$i],
                            'max_score'     => $validatedData['score'][$i],
                        ]);
                    }
                }
            }
        }

        return redirect()->route('admin.page')->with('success', 'Milestone and Rubric added successfully.');
    }

    // get milestone 
    public function getMilestone($id)
    {
        $milestone = Milestone::findOrFail($id);
        $certificate = Certificate::where('milestone_id', $milestone->id)->first();
        $rubric = Rubric::where('milestone_id', $milestone->id)->with('criteria')->first();

        return response()->json([
            'id'                     => $milestone->id,
            'milestone_title'        => $milestone->milestone_title,
            'milestone_description'  => $milestone->milestone_description,
            'capstone_stage_id'      => $milestone->capstone_stage_id,
            'step_order'             => $milestone->step_order,
            'start_date'             => $milestone->start_date,
            'due_date'               => $milestone->due_date,
            'certificate'            => $certificate ? [
                'id'                       => $certificate->id,
                'certificate_title'        => $certificate->certificate_title,
                'certificate_description'  => $certificate->certificate_description,
            ] : null,
            'rubric'                 => $rubric ? [
                'id'            => $rubric->id,
                'rubric_name'   => $rubric->rubric_name,
                'criteria'      => $rubric->criteria->map(fn($c) => [
                    'id'            => $c->id,
                    'criteria_name' => $c->criteria_name,
                    'weight'        => $c->weight,
                    'max_score'     => $c->max_score,
                ])
            ] : null,
        ]);
    }


    // update milestone
    public function updateMilestone(Request $request, $id)
    {
        $milestone = Milestone::findOrFail($id);

        $validated = $request->validate([
            'milestone_title'          => 'required|string|max:255',    
            'capstone_stage'           => 'required|integer|exists:capstone_stages,id',
            'order'                    => [
                'required',
                'integer',
                Rule::unique('milestones', 'step_order')
                    ->ignore($milestone->id)
                    ->where('capstone_stage_id', $request->input('capstone_stage'))
            ],
            'description'              => 'required|string|max:255',
            'start_date'               => 'required|date',
            'due_date'                 => 'required|date|after_or_equal:start_date',
            'has_certificate'          => 'nullable|boolean',
            'certificate_title'        => 'required_if:has_certificate,1|nullable|string|max:255',
            'certificate_description'  => 'required_if:has_certificate,1|nullable|string',
            'document_type' => 'nullable|in:recommendation,approval',

            // Optional Rubric fields
            'add_rubric'               => 'nullable|string',
            'rubric_name'              => 'nullable|required_if:add_rubric,on|string|max:255',
            'criteria_name'            => 'nullable|required_if:add_rubric,on|array',
            'criteria_name.*'          => 'nullable|required_if:add_rubric,on|string|max:255',
            'weight'                   => 'nullable|required_if:add_rubric,on|array',
            'weight.*'                 => 'nullable|required_if:add_rubric,on|numeric|min:0|max:100',
            'score'                    => 'nullable|required_if:add_rubric,on|array',
            'score.*'                  => 'nullable|required_if:add_rubric,on|numeric|min:0',
        ]);

        $milestone->update([
            'milestone_title'        => $validated['milestone_title'],
            'capstone_stage_id'      => $validated['capstone_stage'],
            'step_order'             => $validated['order'],
            'milestone_description'  => $validated['description'],
            'start_date'             => $validated['start_date'],
            'due_date'               => $validated['due_date'],
        ]);

        if (!empty($validated['has_certificate'])) {
            // One certificate per milestone — create it if missing, otherwise update in place
            Certificate::updateOrCreate(
                ['milestone_id' => $milestone->id],
                [
                    'certificate_title'       => $validated['certificate_title'],
                    'certificate_description' => $validated['certificate_description'],
                    'is_locked'               => 1,
                ]
            );
        } else {
            // Admin unchecked "award a certificate" — remove any certificate tied to this milestone
            Certificate::where('milestone_id', $milestone->id)->delete();
        }

        if ($request->has('add_rubric') && $request->add_rubric === 'on') {
            if (round(array_sum($request->input('weight') ?? []), 2) != 100) {
                return response()->json([
                    'errors' => ['weight' => ['Criteria weights must add up to 100%.']]
                ], 422);
            }

            // Update or Create Rubric
            $rubric = Rubric::updateOrCreate(
                ['milestone_id' => $milestone->id],
                ['rubric_name'  => $request->input('rubric_name') ?: ($milestone->milestone_title . ' Rubric')]
            );

            // Clear existing criteria and recreate
            $rubric->criteria()->delete();

            if (!empty($request->input('criteria_name'))) {
                foreach ($request->input('criteria_name') as $i => $name) {
                    if ($name) {
                        $rubric->criteria()->create([
                            'criteria_name' => $name,
                            'weight'        => $request->input('weight')[$i],
                            'max_score'     => $request->input('score')[$i],
                        ]);
                    }
                }
            }
        } else {
            // If "add_rubric" is unchecked, let's delete the rubric and its criteria if they exist
            $existingRubric = Rubric::where('milestone_id', $milestone->id)->first();
            if ($existingRubric) {
                $existingRubric->criteria()->delete();
                $existingRubric->delete();
            }
        }
        if ($request->has_certificate) {
        $titleMap = ['recommendation' => 'Recommendation Sheet', 'approval' => 'Approval Sheet'];
        $certificate = $milestone->certificate()->updateOrCreate([], [
            'document_type' => $request->document_type,
            'certificate_title' => $titleMap[$request->document_type] ?? null,
            'certificate_description' => $request->certificate_description,
        ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Milestone updated successfully.'
            ]);
        }
        return redirect()->route('admin.page')->with('success', 'Milestone updated successfully.');
    }

    // reorder milestones
    public function reorderMilestones(Request $request)
    {
        $validated = $request->validate([
            'milestone_ids' => 'required|array|min:1',
            'milestone_ids.*' => 'required|exists:milestones,id',
        ]);

        $milestones = Milestone::whereIn('id', $validated['milestone_ids'])
            ->with('capstoneStage')
            ->get()
            ->keyBy('id');

        // Determine the stage_type of the milestones being reordered
        $stageTypes = $milestones->map(fn($m) => $m->capstoneStage->stage_type ?? null)->unique()->filter();

        if ($stageTypes->count() > 1) {
            $message = 'Cannot rearrange: milestones must all belong to the same Capstone stage.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'error' => $message], 422);
            }
            return back()->with('error', $message);
        }

        foreach ($validated['milestone_ids'] as $index => $id) {
            Milestone::where('id', $id)->update(['step_order' => $index + 1]);
        }

        $message = 'Milestones rearranged successfully.';
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }
        return back()->with('success', $message);
    }
    /**
     * Delete a milestone and its associated rubric, criteria, and group progress records.
     */
    public function deleteMilestone(Request $request)
    {
        $validated = $request->validate([
            'milestone_id'   => 'required|integer|exists:milestones,id',
            'admin_password' => 'required|string',
        ]);

        // Verify admin password
        if (!Hash::check($validated['admin_password'], Auth::user()->password)) {
            return back()
                ->withErrors(['admin_password' => 'Incorrect password. Milestone was not deleted.'])
                ->withInput();
        }

        $milestone = Milestone::findOrFail($validated['milestone_id']);

        DB::transaction(function () use ($milestone) {
            // 1. Delete associated rubrics and their criteria
            $rubrics = Rubric::where('milestone_id', $milestone->id)->get();
            foreach ($rubrics as $rubric) {
                $rubric->criteria()->delete();
                $rubric->delete();
            }

            // 2. Delete any certificates linked to this milestone
            Certificate::where('milestone_id', $milestone->id)->delete();

            // 3. Delete group milestones (progress) records
            GroupMilestones::where('milestone_id', $milestone->id)->delete();

            // 4. Delete any evaluations, remarks, absences tied to this milestone
            Evaluation::where('milestone_id', $milestone->id)->delete();
            \App\Models\Remarks::where('milestone_id', $milestone->id)->delete();
            \App\Models\Absence::where('milestone_id', $milestone->id)->delete();

            // 5. Finally delete the milestone itself
            $milestone->delete();
        });

        return back()->with('success', 'Milestone and all associated data deleted successfully.');
    }


    // ── ASSIGN / CHANGE ADVISER ────────────────────────────────────
    public function assignGroups(Request $request)
    {
        $validated = $request->validate([
            'group_id'   => 'required|exists:groups,id',
            'adviser_id' => 'required|exists:teachers,id',
        ]);

        Group::where('id', $validated['group_id'])->update(['adviser_id' => $validated['adviser_id']]);

        return back()->with('success', 'Adviser changed successfully.');
    }

    // ── ASSIGN / CHANGE SECTION TEACHER ────────────────────────────
    public function assignSection(Request $request)
    {
        $validated = $request->validate([
            'section_id'      => 'required|exists:sections,id',
            'teacher_user_id' => 'required|string',
        ]);

        $section = Section::findOrFail($validated['section_id']);

        if ($validated['teacher_user_id'] === 'none') {
            $section->user_id = null;
        } else {
            $teacherExists = Teacher::where('user_id', $validated['teacher_user_id'])->exists();
            if (!$teacherExists) {
                return back()->withErrors(['teacher_user_id' => 'The selected teacher does not exist.']);
            }
            $section->user_id = $validated['teacher_user_id'];
        }

        $section->save();

        return back()->with('success', 'Section assigned successfully.');
    }

        // ── GROUP GET/UPDATE (admin) ─────────────────────────────────
    public function getGroupAdmin($id)
    {
        $group = Group::with(['adviser', 'section', 'team_members.student.user'])->findOrFail($id);

        return response()->json([
            'id'             => $group->id,
            'group_name'     => $group->group_name,
            'capstone_title' => $group->capstone_title,
            'adviser_id'     => $group->adviser_id,
            'section_id'     => $group->section_id,
            'section_name'   => $group->section->section_name ?? null,
            'members'        => $group->team_members->map(fn($tm) => [
                'user_id' => $tm->user_id,
                'name'    => trim(
                    (optional($tm->student)->student_first_name ?? '') . ' ' .
                    (optional($tm->student)->student_last_name ?? '')
                ),
                'role'    => $tm->role,
            ]),
        ]);
    }
        // deletegroup
    public function deleteGroup(Request $request)
    {
        $validated = $request->validate([
            'group_id'       => 'required|integer|exists:groups,id',
            'admin_password' => 'required|string',
        ]);

        if (!Hash::check($validated['admin_password'], Auth::user()->password)) {
            return back()
                ->withErrors(['admin_password' => 'Incorrect password. Group was not deleted.'])
                ->withInput();
        }

        $group = Group::findOrFail($validated['group_id']);

        DB::transaction(function () use ($group) {
            Evaluation::where('group_id', $group->id)->delete();
            \App\Models\Remarks::where('group_id', $group->id)->delete();
            \App\Models\Absence::where('group_id', $group->id)->delete();
            GroupMilestones::where('group_id', $group->id)->delete();
            GroupCertificate::where('group_id', $group->id)->delete();
            \App\Models\Revision::where('group_id', $group->id)->each(function ($rev) {
                $rev->documentation()->delete();
                $rev->enhancements()->delete();
                $rev->objectives()->delete();
                $rev->delete();
            });
            TeamMember::where('group_id', $group->id)->delete();
            $group->delete();
        });

        return back()->with('success', 'Group deleted successfully.');
    }   
    //update group admin
        public function updateGroupAdmin(Request $request, $id)
    {
        $group = Group::findOrFail($id);

        $validated = $request->validate([
            'group_name'          => 'required|string|max:255|unique:groups,group_name,' . $group->id,
            'capstone_title'      => 'required|string|max:255|unique:groups,capstone_title,' . $group->id,
            'adviser_id'          => 'required|exists:teachers,id',
            'students'            => 'required|array|min:1',
            'students.*.user_id'  => 'exists:students,user_id',
            'students.*.role'     => 'required|string|in:programmer,designer,researcher',
        ]);

        foreach ($validated['students'] as $studentData) {
            $inOtherGroup = TeamMember::where('user_id', $studentData['user_id'])
                ->where('group_id', '!=', $group->id)
                ->exists();
            if ($inOtherGroup) {
                $student = Student::where('user_id', $studentData['user_id'])->first();
                return response()->json([
                    'errors' => ['students' => ["Student {$student->student_first_name} {$student->student_last_name} is already in another group."]]
                ], 422);
            }
        }

        $group->update([
            'group_name'     => $validated['group_name'],
            'capstone_title' => $validated['capstone_title'],
            'adviser_id'     => $validated['adviser_id'],
        ]);

        TeamMember::where('group_id', $group->id)->delete();
        foreach ($validated['students'] as $studentData) {
            TeamMember::create([
                'group_id' => $group->id,
                'user_id'  => $studentData['user_id'],
                'role'     => $studentData['role'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Group updated successfully.'
        ]);
    }


        public function getTeacherGroups($teacherId)
    {
        $teacher = Teacher::where('user_id', $teacherId)->first();
        if (!$teacher) return response()->json([]);

        return response()->json(Group::where('adviser_id', $teacher->id)->pluck('id'));
    }

        // ── AUTO CHECK SECTIONS ─────────────────────────────────────────
    public function getTeacherSections($teacherId)
    {
        $teacher = Teacher::where('user_id', $teacherId)->first();
        if (!$teacher) {
            return response()->json([]);
        }

        // Return an array of section IDs that this teacher already has
        $sectionIds = $teacher->sections->pluck('id')->toArray();
        return response()->json($sectionIds);
    }



        // ── STUDENT IMPORT ──────────────────────────────────────────
    public function importStudents(Request $request)
    {
        $request->validate([
            'file' => 'required|file|extensions:xlsx,csv,xls',
        ]);

        try {
            $import = new StudentsImport;
            Excel::import($import, $request->file('file'));

            if ($import->failures()->isNotEmpty()) {
                $messages = $import->failures()->map(function ($failure) {
                    return "Row {$failure->row()}: " . implode(', ', $failure->errors());
                });
                return back()->withErrors($messages)
                    ->with('import_students', true)
                    ->with('error', 'Student import was unsuccessful.');
            }

            return back()->with('success', 'Students imported successfully.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Student Import Error: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return back()
                ->withErrors(['import_error' => 'Import Error: ' . $e->getMessage()])
                ->with('import_students', true)
                ->with('error', 'Student import failed. ' . $e->getMessage());
        }
    }

    public function downloadStudentTemplate()
    {
        $headers = ['student_id', 'student_first_name', 'student_middle_name', 'student_last_name', 'course', 'section'];

        $exampleStudents = [
            ['2021-0001', 'Example', 'The', 'Student', 'BSIT', 'East'],
        ];

        return response()->streamDownload(function () use ($headers, $exampleStudents) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            foreach ($exampleStudents as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, 'student_import_template.csv');
    }

    // ── TEACHER IMPORT ───────────────────────────────────────────
    public function importTeachers(Request $request)
    {
        $request->validate([
            'file' => 'required|file|extensions:xlsx,csv,xls',
        ]);

        try {
            $import = new TeachersImport;
            Excel::import($import, $request->file('file'));

            if ($import->failures()->isNotEmpty()) {
                $messages = $import->failures()->map(function ($failure) {
                    return "Row {$failure->row()}: " . implode(', ', $failure->errors());
                });
                return back()->withErrors($messages)
                    ->with('import_teachers', true)
                    ->with('error', 'Teacher import was unsuccessful.');
            }

            return back()->with('success', 'Teachers imported successfully.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Teacher Import Error: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return back()
                ->withErrors(['import_error' => 'Import Error: ' . $e->getMessage()])
                ->with('import_teachers', true)
                ->with('error', 'Teacher import failed. ' . $e->getMessage());
        }
    }

    public function downloadTeacherTemplate()
    {
        $headers = ['teacher_id', 'teacher_first_name', 'teacher_middle_name', 'teacher_last_name'];
        
        $exampleRows = [
        ['TCH-001', 'Example', 'Kani', 'Siya'],
     ];

        return response()->streamDownload(function () use ($headers, $exampleRows) {
        $handle = fopen('php://output', 'w');
        fputcsv($handle, $headers);
        foreach ($exampleRows as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);
        }, 'teacher_import_template.csv');
    }

//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


    public function createRoom(Request $request)
{
    $validated = $request->validate([
        'room_count'            => 'required|integer|min:1',
        'required_milestone_id' => 'required|exists:milestones,id',
        'activity_name'         => 'nullable|string|max:255',
        'panelists'             => 'nullable|array',
        'panelists.*'           => 'exists:teachers,id',
    ]);

    $panelists = $validated['panelists'] ?? [];
    $roomCount = $validated['room_count'];

    $milestone = \App\Models\Milestone::findOrFail($validated['required_milestone_id']);
    $activityName = $validated['activity_name'] ?? $milestone->milestone_title;

    $selectedMilestone = \App\Models\Milestone::findOrFail($validated['required_milestone_id']);
    $stageId = $selectedMilestone->capstone_stage_id;

    $isOralPresentation = in_array(strtoupper($selectedMilestone->milestone_title), [
        'CAPSTONE ORAL PRESENTATION',
        'CAPSTONE PROJECT 2 ORAL PRESENTATION'
    ]);

    $qualificationMilestoneId = $validated['required_milestone_id'];

    if ($isOralPresentation) {
        $recomMilestone = \App\Models\Milestone::where('capstone_stage_id', $stageId)
            ->where(function($q) {
                $q->where('milestone_title', 'like', '%ISSUANCE OF RECOMMENDATION SHEET%')
                  ->orWhere('milestone_title', 'like', '%Recommendation Sheet%');
            })
            ->first();

        if ($recomMilestone) {
            $qualificationMilestoneId = $recomMilestone->id;
        }
    }

    foreach ($panelists as $teacherId) {
        $alreadyAssigned = DB::table('room_panelists')->where('teacher_id', $teacherId)->exists();
        if ($alreadyAssigned) {
            $teacherObj = Teacher::find($teacherId);
            $teacherName = $teacherObj ? ($teacherObj->teacher_first_name . ' ' . $teacherObj->teacher_last_name) : 'Selected Teacher';
            return back()->withErrors(['panelists' => "Teacher {$teacherName} is already assigned to another evaluation room."])->withInput();
        }
    }

    DB::transaction(function () use ($validated, $panelists, $roomCount, $activityName, $qualificationMilestoneId) {
        $requiredMilestoneId = $validated['required_milestone_id'];

        // Get all groups without a room that have completed the qualification milestone
        $groups = Group::whereNull('room_id')
            ->whereHas('groupMilestones', function ($query) use ($qualificationMilestoneId) {
                $query->where('milestone_id', $qualificationMilestoneId)
                      ->where('status', 'completed');
            })->get();

        // Find the highest existing "Room N" number so new rooms continue the sequence
        $maxRoomNumber = EvaluationRoom::where('room_name', 'like', 'Room %')
            ->pluck('room_name')
            ->map(function ($name) {
                return preg_match('/^Room\s+(\d+)$/i', trim($name), $m) ? (int) $m[1] : 0;
            })
            ->max() ?? 0;

        $rooms = [];

        for ($i = 1; $i <= $roomCount; $i++) {
            $roomName = 'Room ' . ($maxRoomNumber + $i);

            $room = EvaluationRoom::create([
                'room_name'             => $roomName,
                'join_code'             => EvaluationRoom::generateUniqueCode(),
                'required_milestone_id' => $requiredMilestoneId,
                'activity_name'         => $activityName,
            ]);
            $rooms[] = $room;
        }

        // Distribute groups round-robin
        if ($groups->isNotEmpty()) {
            $groups->each(function ($group, $index) use ($rooms) {
                $room = $rooms[$index % count($rooms)];
                $group->room_id = $room->id;
                $group->save();
            });
        }

        // Distribute panelists round-robin
        foreach ($panelists as $index => $teacherId) {
            $room = $rooms[$index % count($rooms)];
            $room->panelists()->attach($teacherId);
        }
    });

    return back()->with('success', 'Evaluation rooms created successfully.');
}

    public function getRoom($roomId)
    {
            $room = EvaluationRoom::with(['panelists', 'groups', 'requiredMilestone'])->findOrFail($roomId);
            
            // Get all assigned teacher IDs across all rooms
            $assignedTeacherIds = DB::table('room_panelists')->pluck('teacher_id')->toArray();
            
            // Available teachers are those not assigned to any room
            $availableTeachers = Teacher::whereNotIn('id', $assignedTeacherIds)->get();

            return response()->json([
                'id'                 => $room->id,
                'room_name'          => $room->room_name,
                'join_code'          => $room->join_code,
                'required_milestone' => $room->requiredMilestone ? $room->requiredMilestone->milestone_title : 'None',
                'activity_name'      => $room->activity_name ?? 'N/A',
                'panelists'          => $room->panelists->map(fn($p) => [
                    'id'   => $p->id,
                    'name' => $p->teacher_first_name . ' ' . $p->teacher_last_name,
                ]),
                'available_teachers' => $availableTeachers->map(fn($t) => [
                    'id'   => $t->id,
                    'name' => $t->teacher_first_name . ' ' . $t->teacher_last_name,
                ]),
            ]);
    }
        /**
     * Add a single panelist to a room manually.
     */
    public function addPanelist(Request $request, $roomId)
    {
    $validated = $request->validate([
        'teacher_id' => 'required|exists:teachers,id',
    ]);

    $room = EvaluationRoom::findOrFail($roomId);
    $teacher = Teacher::findOrFail($validated['teacher_id']);

    $alreadyThisTeacher = $room->panelists()->where('teacher_id', $teacher->id)->exists();
    if (!$alreadyThisTeacher) {
        // Check if teacher is already assigned to ANY room
        $isAlreadyAssigned = DB::table('room_panelists')->where('teacher_id', $teacher->id)->exists();
        if ($isAlreadyAssigned) {
            return response()->json(['error' => 'This teacher is already assigned to an evaluation room.'], 422);
        }

        // Add panelist to this room (avoiding duplicate entries)
        $room->panelists()->attach($teacher->id);
    }
                
    return response()->json([
        'success' => true,
        'panelist' => [
            'id'   => $teacher->id,
            'name' => $teacher->teacher_first_name . ' ' . $teacher->teacher_last_name,
        ],
    ]);
    }

    public function removePanelist($roomId, $teacherId)
    {
        EvaluationRoom::findOrFail($roomId)->panelists()->detach($teacherId);
        return response()->json(['success' => true]);
    }

    public function deleteRoom(Request $request)
    {
        $validated = $request->validate([
            'room_id'        => 'required|integer|exists:evaluation_rooms,id',
            'admin_password' => 'required|string',
        ]);

        $adminUser = Auth::user();

        if (!Hash::check($validated['admin_password'], $adminUser->password)) {
            return back()
                ->withErrors(['admin_password' => 'Incorrect password. Evaluation room was not deleted.'])
                ->withInput();
        }

        EvaluationRoom::findOrFail($validated['room_id'])->delete();

        return back()->with('success', 'Evaluation room deleted successfully.');
    }
    
    /**
     * Create N evaluation rooms, distribute selected panelists round-robin across
     * them (each panelist ends up in exactly ONE room — any prior room membership
     * is detached first), then divide all groups evenly among the new rooms.
     */
   /**
 * Regenerate a room's join code (admin only).
 */
    public function regenerateRoomCode($roomId)
    {
        $room = EvaluationRoom::findOrFail($roomId);
        $room->join_code = EvaluationRoom::generateUniqueCode();
        $room->save();

        return response()->json(['success' => true, 'join_code' => $room->join_code]);
    }






        // ── ADMIN PROFILE UPDATE ──────────────────────────────────────
    public function adminProfileUpdate(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            return redirect('/');
        }

        $admin = Admin::where('user_id', $user->user_id)->first();
        if (!$admin) {
            return redirect('/')->with('error', 'Admin profile not found.');
        }

        $validatedData = $request->validate([
            'admin_first_name'  => 'required|string|max:255',
            'admin_last_name'   => 'required|string|max:255',
            'admin_middle_name' => 'nullable|string|max:255',
            'contact_number'    => 'nullable|string|max:20',
            'admin_email'       => 'required|email|unique:admin,admin_email,' . $admin->id,
        ]);

        $admin->update($validatedData);
        $admin->save();

        return redirect()->route('admin.page')->with('success', 'Profile updated successfully.');
    }









        // ── TOGGLE CAPSTONE STAGE ───────────────────────────────────────
   public function toggleCapstoneStage(Request $request)
{
    if (Auth::user()->role !== 'admin') {
        abort(403, 'Unauthorized.');
    }
 
    $request->validate([
        'stage_id' => 'required|exists:capstone_stages,id',
    ]);
 
    $stage = CapstoneStages::findOrFail($request->stage_id);
 
    $stage->is_enabled = ! $stage->is_enabled;
    $stage->save();
 
    // Single source of truth: mirror this stage's status onto its
    // capstone_years row so the dashboard header and any other reader of
    // capstone_years.capstone_1_enabled/2_enabled stay correct.
    if ($stage->capstone_year_id) {
        $field = $stage->stage_type == 1 ? 'capstone_1_enabled' : 'capstone_2_enabled';
        CapstoneYear::where('id', $stage->capstone_year_id)->update([$field => $stage->is_enabled]);
    }
 
    $status = $stage->is_enabled ? 'enabled' : 'disabled';
    return back()->with('success', "{$stage->stage_title} has been {$status} successfully.");
}
    // ── ARCHIVE CAPSTONE BY YEAR ────────────────────────────────────
    public function archiveCapstoneByYear(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'year'      => 'required|integer|min:2000|max:2099',
            'new_title' => 'nullable|string|max:255',
        ]);

        $year = $request->year;

        // Find all active capstone stages
        $activeStages = CapstoneStages::where('is_archived', false)->get();
        $totalCount = 0;
        
        foreach ($activeStages as $stage) {
            // Find and archive active groups pointing to this stage
            $groupsToArchive = Group::where('capstone_stage_id', $stage->id)->where('is_archived', false)->get();
            foreach ($groupsToArchive as $g) {
                $g->is_archived = true;
                $g->archived_year = $year;
                $g->save();

                // Check adviser of this archived group
                $adviserId = $g->adviser_id;
                $hasActiveGroups = Group::where('adviser_id', $adviserId)->where('is_archived', false)->exists();
                if (!$hasActiveGroups) {
                    Teacher::where('id', $adviserId)->update(['is_archived' => true]);
                }

                // Check section of this archived group
                $secId = $g->section_id;
                $hasActiveGroupsSec = Group::where('section_id', $secId)->where('is_archived', false)->exists();
                if (!$hasActiveGroupsSec) {
                    Section::where('id', $secId)->update(['is_archived' => true]);
                }
            }

            // Archive all students in these groups
            $archivedGroupIds = $groupsToArchive->pluck('id')->toArray();
            $studentUserIds = TeamMember::whereIn('group_id', $archivedGroupIds)->pluck('user_id')->toArray();
            Student::whereIn('user_id', $studentUserIds)->update(['is_archived' => true]);

            $totalCount += $groupsToArchive->count();

            // Mark the stage as archived
            $stage->is_archived = true;
            $stage->archived_year = $year;
            $stage->is_enabled = false;
            $stage->save();

            // Create new active cycle stage and clone milestones
            $clonedTitle = "Capstone {$stage->stage_type} - Cycle {$year}";
            $newStage = CapstoneStages::create([
                'stage_title'   => $clonedTitle,
                'is_enabled'    => $stage->stage_type == 1 ? true : false, // Enable stage 1 as default
                'is_archived'   => false,
                'stage_type'    => $stage->stage_type,
            ]);

            // Duplicate milestones
            $oldMilestones = Milestone::where('capstone_stage_id', $stage->id)->get();

            // Delete active classrooms associated with the archived milestones of this stage
            $oldMilestoneIds = $oldMilestones->pluck('id')->toArray();
            EvaluationRoom::whereIn('required_milestone_id', $oldMilestoneIds)->delete();

            foreach ($oldMilestones as $om) {
                Milestone::create([
                    'milestone_title'       => $om->milestone_title,
                    'milestone_description' => $om->milestone_description,
                    'capstone_stage_id'     => $newStage->id,
                    'start_date'            => $om->start_date,
                    'due_date'              => $om->due_date,
                    'step_order'            => $om->step_order,
                ]);
            }
        }

        // Also automatically add this year to custom years list
        $customYears = \App\Models\Setting::get('custom_years', '');
        $yearsArray = $customYears ? explode(',', $customYears) : [];
        if (!in_array($year, $yearsArray)) {
            $yearsArray[] = $year;
            sort($yearsArray);
            \App\Models\Setting::set('custom_years', implode(',', $yearsArray));
        }

        return back()->with('success', "Successfully archived all active Capstone stages and {$totalCount} group(s) for year {$year}, and created new active cycles with duplicated milestone templates.");
    }

    /**
     * Enable/Activate a specific capstone year.
     */
    public function enableCapstoneYear(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'year' => 'required|string',
        ]);

        $yearModel = CapstoneYear::where('year', $request->year)->first();
        if ($yearModel) {
            return $this->activateCapstoneYear($yearModel->id);
        }

        \App\Models\Setting::set('active_year', $request->year);
        return back()->with('success', "Capstone year {$request->year} has been enabled successfully.");
    }

    
    /**
     * Add a new capstone year.
     */
    public function addCapstoneYear(Request $request)
{
    if (Auth::user()->role !== 'admin') {
        abort(403, 'Unauthorized.');
    }

    $validated = $request->validate([
        'year'                => 'required|string|regex:/^\d{4}/',
        'capstone_1_enabled'  => 'nullable|boolean',
        'capstone_2_enabled'  => 'nullable|boolean',
        'is_active'           => 'nullable|boolean',
    ]);

    $year = trim(preg_replace('/[–—]/', '-', $validated['year']));

    if (!preg_match('/^\d{4}-\d{4}$/', $year)) {
        return back()->withErrors(['year' => 'Invalid year format. Use e.g., 2026-2027.']);
    }

    if (CapstoneYear::where('year', $year)->exists()) {
        return back()->withErrors(['year' => "Capstone year {$year} already exists."]);
    }

    $isActive = !empty($request->is_active);

    DB::transaction(function () use ($year, $isActive, $request) {

        // ── archive any previously active year ────────────────────────
        if ($isActive) {
            $previouslyActive = CapstoneYear::where('is_active', true)->get();

            foreach ($previouslyActive as $prevYear) {
                $prevYear->update(['is_active' => false, 'archived_at' => now()]);

                Group::where('capstone_year_id', $prevYear->id)->update(['is_archived' => true]);
                Student::where('capstone_year_id', $prevYear->id)->update(['is_archived' => true]);

                CapstoneStages::where('capstone_year_id', $prevYear->id)->update([
                    'is_archived'   => true,
                    'archived_year' => (int) substr($prevYear->year, 0, 4),
                ]);

                $this->archiveRoomsForYear($prevYear->id);
            }
        }

        // ── create the new year row ───────────────────────────────────
        $newYear = CapstoneYear::create([
            'year'                => $year,
            'is_active'           => $isActive,
            'capstone_1_enabled'  => $request->has('capstone_1_enabled'),
            'capstone_2_enabled'  => $request->has('capstone_2_enabled'),
            'archived_at'         => $isActive ? null : now(),
        ]);

        // ── create the C1 / C2 stages for the new year ────────────────
        $c1Stage = CapstoneStages::create([
            'stage_title'      => "Capstone 1 - {$year}",
            'stage_type'       => 1,
            'is_enabled'       => $newYear->capstone_1_enabled,
            'is_archived'      => !$isActive,
            'archived_year'    => $isActive ? null : (int) substr($year, 0, 4),
            'capstone_year_id' => $newYear->id,
        ]);

        $c2Stage = CapstoneStages::create([
            'stage_title'      => "Capstone 2 - {$year}",
            'stage_type'       => 2,
            'is_enabled'       => $newYear->capstone_2_enabled,
            'is_archived'      => !$isActive,
            'archived_year'    => $isActive ? null : (int) substr($year, 0, 4),
            'capstone_year_id' => $newYear->id,
        ]);

        // ── clone milestones + rubrics + criteria for each stage ──────
        $this->cloneStageMilestonesAndRubrics(1, $c1Stage, 'Proposal hearing');
        $this->cloneStageMilestonesAndRubrics(2, $c2Stage, 'Oral presentation');

        // ── register the year in settings ─────────────────────────────
        $customYears = \App\Models\Setting::get('custom_years', '');
        $yearsArray  = $customYears ? explode(',', $customYears) : [];
        if (!in_array($year, $yearsArray)) {
            $yearsArray[] = $year;
            sort($yearsArray);
            \App\Models\Setting::set('custom_years', implode(',', $yearsArray));
        }
        if ($isActive) {
            \App\Models\Setting::set('active_year', $year);
        }
    });

    return back()->with('success', "Capstone year {$year} added successfully.");
}
/**
 * Clone every milestone (and its attached rubric + criteria) from the most
 * recent stage of the given $stageType into the given $newStage.
 * Falls back to a default milestone if no prior stage exists.
 */
private function cloneStageMilestonesAndRubrics(int $stageType, CapstoneStages $newStage, string $fallbackTitle): void
{
    // Latest stage of this type that actually HAS milestones
    $sourceStage = CapstoneStages::where('stage_type', $stageType)
        ->where('id', '!=', $newStage->id)
        ->whereHas('milestones')   // see note below
        ->latest('id')
        ->first();

    // Fallback: no prior stage with milestones
    if (! $sourceStage) {
        $title = $stageType === 1 ? 'Capstone Oral Presentation' : 'CAPSTONE PROJECT 2 ORAL PRESENTATION';

        $m = Milestone::create([
            'milestone_title'       => $title,
            'milestone_description' => $title . ' milestone',
            'capstone_stage_id'     => $newStage->id,
            'step_order'            => 1,
            'start_date'            => now()->toDateString(),
            'due_date'              => now()->addDays(14)->toDateString(),
        ]);
        DefaultRubrics::attachTo($m);
        return;
    }

    foreach (Milestone::where('capstone_stage_id', $sourceStage->id)->get() as $old) {
        $new = Milestone::create([
            'milestone_title'       => $old->milestone_title,
            'milestone_description' => $old->milestone_description,
            'capstone_stage_id'     => $newStage->id,
            'step_order'            => $old->step_order,
            'start_date'            => $old->start_date,
            'due_date'              => $old->due_date,
        ]);

        $oldRubric = Rubric::where('milestone_id', $old->id)->with('criteria')->first();

        if ($oldRubric) {
            $newRubric = Rubric::create([
                'rubric_name'  => $oldRubric->rubric_name,
                'milestone_id' => $new->id,
            ]);
            foreach ($oldRubric->criteria as $c) {
                RubricCriteria::create([
                    'rubric_id'     => $newRubric->id,
                    'criteria_name' => $c->criteria_name,
                    'weight'        => $c->weight,
                    'max_score'     => $c->max_score,
                ]);
            }
        } else {
            // Source had no rubric (e.g. older year): fall back to the defaults
            DefaultRubrics::attachTo($new);
        }
    }
}
    /**
     * Activate a capstone year, archiving the current one and restoring groups/students.
     */
    public function activateCapstoneYear($id)
{
    if (Auth::user()->role !== 'admin') {
        abort(403, 'Unauthorized.');
    }
 
    $targetYear = CapstoneYear::findOrFail($id);
 
    DB::transaction(function () use ($targetYear) {
        // Archive whatever year(s) are currently active
        $activeYears = CapstoneYear::where('is_active', true)
            ->where('id', '!=', $targetYear->id)
            ->get();
 
        foreach ($activeYears as $activeYear) {
            $activeYear->update([
                'is_active' => false,
                'archived_at' => now(),
            ]);
 
            Group::where('capstone_year_id', $activeYear->id)->update(['is_archived' => true]);
            Student::where('capstone_year_id', $activeYear->id)->update(['is_archived' => true]);
 
            // Cascade: Capstone 1 & 2 records under this year are now archived
            CapstoneStages::where('capstone_year_id', $activeYear->id)->update([
                'is_archived' => true,
                'archived_year' => (int) substr($activeYear->year, 0, 4),
            ]);
            $this->archiveRoomsForYear($activeYear->id);
        }
 
        // Activate target year
        $targetYear->update([
            'is_active' => true,
            'archived_at' => null,
        ]);
 
        \App\Models\Setting::set('active_year', $targetYear->year);
 
        // Restore target year groups, students, and its Capstone 1/2 records
        Group::where('capstone_year_id', $targetYear->id)->update(['is_archived' => false]);
        Student::where('capstone_year_id', $targetYear->id)->update(['is_archived' => false]);
        CapstoneStages::where('capstone_year_id', $targetYear->id)->update([
            'is_archived' => false,
            'archived_year' => null,
        ]);
        $this->restoreRoomsForYear($targetYear->id); 
    });
 
    return back()->with('success', "Capstone year {$targetYear->year} has been successfully activated.");
}
 

    /**
     * Archive a capstone year.
     */
 public function archiveCapstoneYear($id)
{
    if (Auth::user()->role !== 'admin') {
        abort(403, 'Unauthorized.');
    }

    $targetYear = CapstoneYear::findOrFail($id);

    DB::transaction(function () use ($targetYear) {
        $targetYear->update([
            'is_active' => false,
            'archived_at' => now(),
        ]);

        Group::where('capstone_year_id', $targetYear->id)->update(['is_archived' => true]);
        Student::where('capstone_year_id', $targetYear->id)->update(['is_archived' => true]);

        CapstoneStages::where('capstone_year_id', $targetYear->id)->update([
            'is_archived' => true,
            'archived_year' => (int) substr($targetYear->year, 0, 4),
        ]);

        $this->archiveRoomsForYear($targetYear->id); // ← added
    });

    return back()->with('success', "Capstone year {$targetYear->year} has been archived.");
}

 /**
     * Restore an archived group and its related entities back to active.
     */
    public function restoreGroup($id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }

        $group = Group::findOrFail($id);

        DB::transaction(function () use ($group) {
            // 1. Restore the group itself
            $group->is_archived = false;
            $group->archived_year = null;

            // Associate with active stage of same stage_type if needed
            if ($group->capstoneStage && $group->capstoneStage->is_archived) {
                $activeStage = CapstoneStages::where('stage_type', $group->capstoneStage->stage_type)
                    ->where('is_archived', false)
                    ->first();
                if ($activeStage) {
                    $group->capstone_stage_id = $activeStage->id;
                }
            }
            $group->save();

            // 2. Restore adviser if archived
            if ($group->adviser_id) {
                Teacher::where('id', $group->adviser_id)->update(['is_archived' => false]);
            }

            // 3. Restore section if archived
            if ($group->section_id) {
                Section::where('id', $group->section_id)->update(['is_archived' => false]);
            }

            // 4. Restore students
            $studentUserIds = TeamMember::where('group_id', $group->id)->pluck('user_id')->toArray();
            Student::whereIn('user_id', $studentUserIds)->update(['is_archived' => false]);
        });

        return back()->with('success', 'Group and its associated records have been successfully restored to active status.');
    }
        /**
     * Permanently delete an archived group and all its related records.
     */
    public function deleteArchivedGroup($id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }

        $group = Group::findOrFail($id);

        DB::transaction(function () use ($group) {
            // 1. Delete associated evaluation records
            Evaluation::where('group_id', $group->id)->delete();

            // 2. Delete associated remarks
            \App\Models\Remarks::where('group_id', $group->id)->delete();

            // 3. Delete associated absences
            \App\Models\Absence::where('group_id', $group->id)->delete();

            // 4. Delete group milestones
            GroupMilestones::where('group_id', $group->id)->delete();

            // 5. Delete group certificates
            GroupCertificate::where('group_id', $group->id)->delete();

            // 6. Delete team members
            TeamMember::where('group_id', $group->id)->delete();

            // 7. Delete the group itself
            $group->delete();
        });

        return back()->with('success', 'Archived group and all of its related records have been permanently deleted.');
    }
        /**
     * Add a new active capstone stage.
     */
    public function addCapstoneStage(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'stage_title' => 'required|string|max:255',
            'stage_type'  => 'required|integer|in:1,2',
            'is_enabled'  => 'nullable|boolean',
        ]);

        $isEnabled = !empty($request->is_enabled);

        if ($isEnabled) {
            // Disable other stages
            CapstoneStages::where('is_archived', false)->update(['is_enabled' => 0]);
        }

        CapstoneStages::create([
            'stage_title' => $validated['stage_title'],
            'stage_type'  => $validated['stage_type'],
            'is_enabled'  => $isEnabled,
            'is_archived' => false,
        ]);

        return back()->with('success', 'Capstone stage created successfully.');
    }

    /**
     * Update an existing capstone stage.
     */
    public function updateCapstoneStage(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }

        $stage = CapstoneStages::findOrFail($id);

        $validated = $request->validate([
            'stage_title' => 'required|string|max:255',
            'stage_type'  => 'required|integer|in:1,2',
        ]);

        $stage->update([
            'stage_title' => $validated['stage_title'],
            'stage_type'  => $validated['stage_type'],
        ]);

        return back()->with('success', 'Capstone stage updated successfully.');
    }

    /**
     * Delete an active capstone stage.
     */
    public function deleteCapstoneStage($id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }

        $stage = CapstoneStages::findOrFail($id);

        // Check if there are groups assigned to this stage
        $assignedGroupsCount = Group::where('capstone_stage_id', $stage->id)->count();
        if ($assignedGroupsCount > 0) {
            return back()->with('error', "Cannot delete stage: {$assignedGroupsCount} group(s) are assigned to this stage.");
        }

        // Check if there are milestones assigned to this stage
        $assignedMilestonesCount = Milestone::where('capstone_stage_id', $stage->id)->count();
        if ($assignedMilestonesCount > 0) {
            return back()->with('error', "Cannot delete stage: {$assignedMilestonesCount} milestone(s) are assigned to this stage.");
        }

        $stage->delete();

        return back()->with('success', 'Capstone stage deleted successfully.');
    }
        /**
     * Update an existing capstone year configurations.
     */
    public function updateCapstoneYear(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }

        $targetYear = CapstoneYear::findOrFail($id);

        $validated = $request->validate([
            'year' => 'required|string|regex:/^\d{4}/',
            'capstone_1_enabled' => 'nullable|boolean',
            'capstone_2_enabled' => 'nullable|boolean',
        ]);

       $year = preg_replace('/[–—]/', '-', $validated['year']);
        $year = trim($year);

        if (!preg_match('/^\d{4}-\d{4}$/', $year)) {
            return back()->withErrors(['year' => 'Invalid year format.']);
        }

            if (CapstoneYear::where('year', $year)->where('id', '!=', $id)->exists()) {
                return back()->withErrors(['year' => "Capstone year {$year} already exists."]);
            }

            $targetYear->update([
                'year' => $year,
                'capstone_1_enabled' => $request->has('capstone_1_enabled'),
                'capstone_2_enabled' => $request->has('capstone_2_enabled'),
            ]);

            return back()->with('success', "Capstone year {$year} updated successfully.");
        }

        /**
         * Safely delete a capstone year and all of its associated records (cascading delete).
         */
    public function deleteCapstoneYear(Request $request, $id)
    {
            if (Auth::user()->role !== 'admin') {
                abort(403, 'Unauthorized.');
            }

            $validated = $request->validate([
                'admin_password' => 'required|string',
            ]);

            if (!Hash::check($validated['admin_password'], Auth::user()->password)) {
                return back()->withErrors(['admin_password' => 'Incorrect password. Capstone year was not deleted.'])->withInput();
            }

            $year = CapstoneYear::findOrFail($id);

            // Prevent deleting the active year
            if ($year->is_active) {
                return back()->with('error', 'Cannot delete the currently active capstone year. Please activate another year first.');
            }

            DB::transaction(function () use ($year) {
                $groupIds = Group::where('capstone_year_id', $year->id)->pluck('id')->toArray();

                // 1. Delete associated progress and evaluation records
                Evaluation::whereIn('group_id', $groupIds)->delete();
                \App\Models\Remarks::whereIn('group_id', $groupIds)->delete();
                \App\Models\Absence::whereIn('group_id', $groupIds)->delete();
                TeamMember::whereIn('group_id', $groupIds)->delete();
                GroupMilestones::whereIn('group_id', $groupIds)->delete();
                GroupCertificate::whereIn('group_id', $groupIds)->delete();

                // 2. Delete students and their associated user profiles
                $studentUserIds = Student::where('capstone_year_id', $year->id)->pluck('user_id')->toArray();
                Student::where('capstone_year_id', $year->id)->delete();
                User::whereIn('user_id', $studentUserIds)->delete();

                // 3. Delete groups
                Group::where('capstone_year_id', $year->id)->delete();

                // 4. Delete capstone stages and their milestones
                $stageIds = CapstoneStages::where('capstone_year_id', $year->id)->pluck('id')->toArray();
                Milestone::whereIn('capstone_stage_id', $stageIds)->delete();
                CapstoneStages::whereIn('id', $stageIds)->delete();

                // 5. Finally, delete the capstone year itself
                $year->delete();
            });

            return back()->with('success', 'Capstone year and all of its associated groups, students, and progress records have been permanently deleted.');
    }

    /**
 * Archive every evaluation room that has a group belonging to the given capstone year.
 */
private function archiveRoomsForYear($capstoneYearId)
{
    $roomIds = Group::where('capstone_year_id', $capstoneYearId)
        ->whereNotNull('room_id')
        ->pluck('room_id')
        ->unique();

    if ($roomIds->isNotEmpty()) {
        EvaluationRoom::whereIn('id', $roomIds)->update([
            'is_archived'   => true,
            'archived_year' => (int) substr(
                CapstoneYear::find($capstoneYearId)->year ?? '', 0, 4
            ),
        ]);
    }
}

/**
 * Restore every evaluation room that has a group belonging to the given capstone year.
 */
private function restoreRoomsForYear($capstoneYearId)
{
    $roomIds = Group::where('capstone_year_id', $capstoneYearId)
        ->whereNotNull('room_id')
        ->pluck('room_id')
        ->unique();

    if ($roomIds->isNotEmpty()) {
        EvaluationRoom::whereIn('id', $roomIds)->update([
            'is_archived'   => false,
            'archived_year' => null,
        ]);
    }
}
}
