<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\UserCourse;
use App\Models\Skill;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with(['skills', 'userCourses' => function ($query) {
            $query->where('user_id', Auth::id());
        }])->get()->map(function ($course) {
            $userCourse = $course->userCourses->first();
            return [
                'id' => $course->id,
                'title' => $course->title,
                'description' => $course->description,
                'provider' => $course->provider,
                'url' => $course->url,
                'duration' => $course->duration,
                'difficulty_level' => $course->difficulty_level,
                'skills_covered' => $course->skills->pluck('name'),
                'completion_status' => $userCourse ? $userCourse->status : 'not_started',
                'progress' => $userCourse ? $userCourse->progress : 0,
                'rating' => $userCourse ? $userCourse->rating : null,
                'reviews_count' => $course->userCourses->count(),
                'price' => $course->price,
                'currency' => $course->currency,
                'certificate_offered' => $course->certificate_offered,
            ];
        });

        return response()->json($courses);
    }

    public function recommended()
    {
        $user = Auth::user();
        
        // Get courses that match user's skills
        $recommendedCourses = Course::select('courses.*')
            ->join('course_skill', 'courses.id', '=', 'course_skill.course_id')
            ->join('user_skills', 'course_skill.skill_id', '=', 'user_skills.skill_id')
            ->where('user_skills.user_id', $user->id)
            ->with(['skills', 'userCourses' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->groupBy('courses.id')
            ->take(6)
            ->get()
            ->map(function ($course) {
                $userCourse = $course->userCourses->first();
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'description' => $course->description,
                    'provider' => $course->provider,
                    'url' => $course->url,
                    'duration' => $course->duration,
                    'difficulty_level' => $course->difficulty_level,
                    'skills_covered' => $course->skills->pluck('name'),
                    'completion_status' => $userCourse ? $userCourse->status : 'not_started',
                    'progress' => $userCourse ? $userCourse->progress : 0,
                    'rating' => $userCourse ? $userCourse->rating : null,
                    'reviews_count' => $course->userCourses->count(),
                    'price' => $course->price,
                    'currency' => $course->currency,
                    'certificate_offered' => $course->certificate_offered,
                ];
            });

        return response()->json($recommendedCourses);
    }

    public function updateProgress(Request $request, Course $course)
    {
        $request->validate([
            'progress' => 'required|integer|min:0|max:100'
        ]);

        $userCourse = UserCourse::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'course_id' => $course->id
            ],
            [
                'progress' => $request->progress,
                'status' => $request->progress >= 100 ? 'completed' : 'in_progress'
            ]
        );

        return response()->json([
            'message' => 'Progress updated successfully',
            'progress' => $userCourse->progress,
            'status' => $userCourse->status
        ]);
    }

    public function rate(Request $request, Course $course)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5'
        ]);

        $userCourse = UserCourse::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'course_id' => $course->id
            ],
            ['rating' => $request->rating]
        );

        return response()->json([
            'message' => 'Rating updated successfully',
            'rating' => $userCourse->rating
        ]);
    }
}
