<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with(['skills', 'careerPaths'])->paginate(10);
        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        $skills = Skill::all();
        return view('admin.courses.create', compact('skills'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'provider' => 'required|string|max:255',
            'url' => 'required|url',
            'duration' => 'required|numeric|min:0',
            'difficulty_level' => 'required|in:beginner,intermediate,advanced',
            'price' => 'required|numeric|min:0',
            'language' => 'required|string|max:255',
            'rating' => 'required|numeric|min:0|max:5',
            'reviews_count' => 'required|integer|min:0',
            'certificate_offered' => 'boolean',
            'metadata' => 'nullable|json',
            'skills' => 'array',
            'skills.*' => 'exists:skills,id',
        ]);

        // Convert metadata from JSON string to array
        if (isset($validated['metadata'])) {
            $validated['metadata'] = json_decode($validated['metadata'], true);
        }

        // Set default value for certificate_offered
        $validated['certificate_offered'] = $request->has('certificate_offered');

        DB::beginTransaction();
        try {
            $course = Course::create($validated);

            // Attach skills with importance level and skill level gained
            if ($request->has('skills')) {
                foreach ($request->skills as $skillId) {
                    $course->skills()->attach($skillId, [
                        'importance_level' => rand(1, 5), // You might want to make this a form field
                        'skill_level_gained' => rand(1, 5), // You might want to make this a form field
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.courses.index')
                ->with('success', 'Course created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to create course. ' . $e->getMessage()]);
        }
    }

    public function edit(Course $course)
    {
        $skills = Skill::all();
        $course->load('skills');
        return view('admin.courses.edit', compact('course', 'skills'));
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'provider' => 'required|string|max:255',
            'url' => 'required|url',
            'duration' => 'required|numeric|min:0',
            'difficulty_level' => 'required|in:beginner,intermediate,advanced',
            'price' => 'required|numeric|min:0',
            'language' => 'required|string|max:255',
            'rating' => 'required|numeric|min:0|max:5',
            'reviews_count' => 'required|integer|min:0',
            'certificate_offered' => 'boolean',
            'metadata' => 'nullable|json',
            'skills' => 'array',
            'skills.*' => 'exists:skills,id',
        ]);

        // Convert metadata from JSON string to array
        if (isset($validated['metadata'])) {
            $validated['metadata'] = json_decode($validated['metadata'], true);
        }

        // Set default value for certificate_offered
        $validated['certificate_offered'] = $request->has('certificate_offered');

        DB::beginTransaction();
        try {
            $course->update($validated);

            // Sync skills with importance level and skill level gained
            if ($request->has('skills')) {
                $skillSync = [];
                foreach ($request->skills as $skillId) {
                    $skillSync[$skillId] = [
                        'importance_level' => rand(1, 5), // You might want to make this a form field
                        'skill_level_gained' => rand(1, 5), // You might want to make this a form field
                    ];
                }
                $course->skills()->sync($skillSync);
            } else {
                $course->skills()->detach();
            }

            DB::commit();
            return redirect()->route('admin.courses.index')
                ->with('success', 'Course updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to update course. ' . $e->getMessage()]);
        }
    }

    public function destroy(Course $course)
    {
        try {
            $course->delete();
            return redirect()->route('admin.courses.index')
                ->with('success', 'Course deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete course. ' . $e->getMessage()]);
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        // Import logic here
        return redirect()->route('admin.courses.index')
            ->with('success', 'Courses imported successfully.');
    }

    public function export()
    {
        // Export logic here
        return response()->download('path/to/courses.csv');
    }
}
