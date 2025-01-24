<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Skill;
use App\Models\Course;
use App\Models\CareerGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Show user's skills
     */
    public function showSkills(User $user)
    {
        $this->authorize('view', $user);
        
        $user->load('skills');
        $availableSkills = Skill::whereNotIn('id', $user->skills->pluck('id'))->get();
        
        return view('users.skills.index', [
            'user' => $user,
            'userSkills' => $user->skills,
            'availableSkills' => $availableSkills,
        ]);
    }

    /**
     * Add a skill to user's profile
     */
    public function addSkill(Request $request, User $user)
    {
        $this->authorize('update', $user);
        
        $validated = $request->validate([
            'skill_id' => ['required', 'exists:skills,id'],
            'proficiency_level' => ['required', 'integer', 'min:1', 'max:5'],
            'target_level' => ['required', 'integer', 'min:1', 'max:5', 'gte:proficiency_level'],
        ]);

        $user->skills()->attach($validated['skill_id'], [
            'proficiency_level' => $validated['proficiency_level'],
            'target_level' => $validated['target_level'],
        ]);

        return back()->with('success', 'Skill added successfully.');
    }

    /**
     * Remove a skill from user's profile
     */
    public function removeSkill(User $user, Skill $skill)
    {
        $this->authorize('update', $user);
        
        $user->skills()->detach($skill->id);
        
        return back()->with('success', 'Skill removed successfully.');
    }

    /**
     * Show user's career goals
     */
    public function showGoals(User $user)
    {
        $this->authorize('view', $user);
        
        $user->load(['careerGoals.careerPath', 'careerGoals.skills']);
        
        return view('users.goals.index', [
            'user' => $user,
            'goals' => $user->careerGoals,
            'careerPaths' => \App\Models\CareerPath::all(),
        ]);
    }

    /**
     * Add a new career goal
     */
    public function addGoal(Request $request, User $user)
    {
        $this->authorize('update', $user);
        
        $validated = $request->validate([
            'career_path_id' => ['required', 'exists:career_paths,id'],
            'target_completion_date' => ['required', 'date', 'after:today'],
            'status' => ['required', 'in:not_started,in_progress,completed,on_hold'],
            'progress' => ['required', 'integer', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $user->careerGoals()->create($validated);

        return back()->with('success', 'Career goal added successfully.');
    }

    /**
     * Remove a career goal
     */
    public function removeGoal(User $user, CareerGoal $goal)
    {
        $this->authorize('update', $user);
        
        if ($goal->user_id !== $user->id) {
            abort(403);
        }
        
        $goal->delete();
        
        return back()->with('success', 'Career goal removed successfully.');
    }

    /**
     * Show user's courses
     */
    public function showCourses(User $user)
    {
        $this->authorize('view', $user);
        
        $user->load('courses');
        $availableCourses = Course::whereNotIn('id', $user->courses->pluck('id'))->get();
        
        return view('users.courses.index', [
            'user' => $user,
            'userCourses' => $user->courses,
            'availableCourses' => $availableCourses,
        ]);
    }

    /**
     * Add a course to user's profile
     */
    public function addCourse(Request $request, User $user)
    {
        $this->authorize('update', $user);
        
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

        return back()->with('success', 'Course added successfully.');
    }

    /**
     * Remove a course from user's profile
     */
    public function removeCourse(User $user, Course $course)
    {
        $this->authorize('update', $user);
        
        $user->courses()->detach($course->id);
        
        return back()->with('success', 'Course removed successfully.');
    }
}
