<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Skill;
use App\Models\Course;
use App\Models\CareerGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'is_admin' => ['boolean'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_admin' => $request->has('is_admin'),
        ]);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user->load(['skills', 'courses', 'careerGoals']);
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_admin' => ['boolean'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if ($validated['password']) {
            $user->password = Hash::make($validated['password']);
        }
        $user->is_admin = $request->has('is_admin');
        $user->save();

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    // Skills Management
    public function showSkills(User $user)
    {
        $skills = $user->skills()->paginate(10);
        $availableSkills = Skill::whereNotIn('id', $user->skills->pluck('id'))->get();
        return view('admin.users.skills', compact('user', 'skills', 'availableSkills'));
    }

    public function addSkill(Request $request, User $user)
    {
        $validated = $request->validate([
            'skill_id' => ['required', 'exists:skills,id'],
            'proficiency_level' => ['required', 'in:beginner,intermediate,advanced,expert'],
        ]);

        $user->skills()->attach($validated['skill_id'], [
            'proficiency_level' => $validated['proficiency_level'],
        ]);

        return redirect()->route('admin.users.skills.index', $user)
            ->with('success', 'Skill added successfully.');
    }

    public function removeSkill(User $user, Skill $skill)
    {
        $user->skills()->detach($skill);
        return redirect()->route('admin.users.skills.index', $user)
            ->with('success', 'Skill removed successfully.');
    }

    // Career Goals Management
    public function showGoals(User $user)
    {
        $goals = $user->careerGoals()->paginate(10);
        return view('admin.users.goals', compact('user', 'goals'));
    }

    public function addGoal(Request $request, User $user)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'target_date' => ['required', 'date'],
            'status' => ['required', 'in:not_started,in_progress,completed'],
        ]);

        $user->careerGoals()->create($validated);

        return redirect()->route('admin.users.goals.index', $user)
            ->with('success', 'Career goal added successfully.');
    }

    public function removeGoal(User $user, CareerGoal $goal)
    {
        $goal->delete();
        return redirect()->route('admin.users.goals.index', $user)
            ->with('success', 'Career goal removed successfully.');
    }

    // Courses Management
    public function showCourses(User $user)
    {
        $courses = $user->courses()->paginate(10);
        $availableCourses = Course::whereNotIn('id', $user->courses->pluck('id'))->get();
        return view('admin.users.courses', compact('user', 'courses', 'availableCourses'));
    }

    public function addCourse(Request $request, User $user)
    {
        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'status' => ['required', 'in:not_started,in_progress,completed'],
            'progress' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $user->courses()->attach($validated['course_id'], [
            'status' => $validated['status'],
            'progress' => $validated['progress'],
            'completion_date' => $validated['status'] === 'completed' ? now() : null,
        ]);

        return redirect()->route('admin.users.courses.index', $user)
            ->with('success', 'Course added successfully.');
    }

    public function removeCourse(User $user, Course $course)
    {
        $user->courses()->detach($course);
        return redirect()->route('admin.users.courses.index', $user)
            ->with('success', 'Course removed successfully.');
    }

    public function export()
    {
        $users = User::with(['skills', 'courses', 'careerGoals'])->get();
        
        $csvData = [];
        $csvData[] = [
            'ID', 'Name', 'Email', 'Is Admin', 'Skills (Level)', 
            'Current Courses (Progress)', 'Career Goals', 
            'Created At', 'Updated At'
        ];

        foreach ($users as $user) {
            $skills = $user->skills->map(function($skill) {
                return $skill->name . ' (' . $skill->pivot->proficiency_level . ')';
            })->implode(', ');

            $courses = $user->courses->map(function($course) {
                return $course->title . ' (' . $course->pivot->progress . '%)';
            })->implode(', ');

            $goals = $user->careerGoals->map(function($goal) {
                return $goal->title . ' (' . $goal->status . ')';
            })->implode(', ');

            $csvData[] = [
                $user->id,
                $user->name,
                $user->email,
                $user->is_admin ? 'Yes' : 'No',
                $skills,
                $courses,
                $goals,
                $user->created_at->format('Y-m-d H:i:s'),
                $user->updated_at->format('Y-m-d H:i:s'),
            ];
        }

        $filename = 'users_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        $handle = fopen($path, 'r');
        $header = true;

        \DB::beginTransaction();

        try {
            while (($data = fgetcsv($handle)) !== false) {
                if ($header) {
                    $header = false;
                    continue;
                }

                $user = User::updateOrCreate(
                    ['email' => $data[2]],
                    [
                        'name' => $data[1],
                        'password' => Hash::make('password'), // Set a default password
                        'is_admin' => strtolower($data[3]) === 'yes',
                    ]
                );

                // Process skills if they exist
                if (!empty($data[4])) {
                    $skillsData = explode(',', $data[4]);
                    foreach ($skillsData as $skillData) {
                        preg_match('/(.+) \((.+)\)/', trim($skillData), $matches);
                        if (count($matches) === 3) {
                            $skillName = trim($matches[1]);
                            $proficiencyLevel = strtolower(trim($matches[2]));
                            
                            $skill = Skill::firstOrCreate(['name' => $skillName]);
                            $user->skills()->syncWithoutDetaching([
                                $skill->id => ['proficiency_level' => $proficiencyLevel]
                            ]);
                        }
                    }
                }

                // Process courses if they exist
                if (!empty($data[5])) {
                    $coursesData = explode(',', $data[5]);
                    foreach ($coursesData as $courseData) {
                        preg_match('/(.+) \((\d+)%\)/', trim($courseData), $matches);
                        if (count($matches) === 3) {
                            $courseTitle = trim($matches[1]);
                            $progress = (int)$matches[2];
                            
                            $course = Course::firstOrCreate(['title' => $courseTitle]);
                            $user->courses()->syncWithoutDetaching([
                                $course->id => [
                                    'progress' => $progress,
                                    'status' => $progress === 100 ? 'completed' : 'in_progress',
                                    'completion_date' => $progress === 100 ? now() : null,
                                ]
                            ]);
                        }
                    }
                }

                // Process career goals if they exist
                if (!empty($data[6])) {
                    $goalsData = explode(',', $data[6]);
                    foreach ($goalsData as $goalData) {
                        preg_match('/(.+) \((.+)\)/', trim($goalData), $matches);
                        if (count($matches) === 3) {
                            $goalTitle = trim($matches[1]);
                            $status = strtolower(trim($matches[2]));
                            
                            $user->careerGoals()->create([
                                'title' => $goalTitle,
                                'status' => $status,
                                'description' => 'Imported from CSV',
                                'target_date' => now()->addMonths(3),
                            ]);
                        }
                    }
                }
            }

            \DB::commit();
            return redirect()->route('admin.users.index')
                ->with('success', 'Users imported successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->route('admin.users.index')
                ->with('error', 'Error importing users: ' . $e->getMessage());
        } finally {
            fclose($handle);
        }
    }
}
