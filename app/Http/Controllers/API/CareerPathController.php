<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CareerPath;
use App\Models\UserCareerPath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CareerPathController extends Controller
{
    public function index()
    {
        $careerPaths = CareerPath::with(['skills', 'courses'])->get()->map(function ($path) {
            return [
                'id' => $path->id,
                'title' => $path->title,
                'description' => $path->description,
                'required_skills' => $path->skills->map(function ($skill) {
                    return [
                        'id' => $skill->id,
                        'name' => $skill->name,
                        'required_level' => $skill->pivot->required_level,
                    ];
                }),
                'courses' => $path->courses->map(function ($course) {
                    return [
                        'id' => $course->id,
                        'title' => $course->title,
                        'order' => $course->pivot->order,
                    ];
                }),
                'estimated_completion_time' => $path->estimated_completion_time,
                'difficulty_level' => $path->difficulty_level,
                'career_outcomes' => $path->career_outcomes,
                'prerequisites' => $path->prerequisites,
            ];
        });

        return response()->json($careerPaths);
    }

    public function userPaths()
    {
        $user = Auth::user();
        $userPaths = $user->careerPaths()->with(['skills', 'courses'])->get()->map(function ($path) use ($user) {
            $progress = $user->getCareerPathProgress($path);
            
            return [
                'id' => $path->id,
                'title' => $path->title,
                'description' => $path->description,
                'progress' => $progress,
                'required_skills' => $path->skills->map(function ($skill) {
                    return [
                        'id' => $skill->id,
                        'name' => $skill->name,
                        'required_level' => $skill->pivot->required_level,
                    ];
                }),
                'courses' => $path->courses->map(function ($course) {
                    return [
                        'id' => $course->id,
                        'title' => $course->title,
                        'order' => $course->pivot->order,
                    ];
                }),
                'estimated_completion_time' => $path->estimated_completion_time,
                'difficulty_level' => $path->difficulty_level,
                'career_outcomes' => $path->career_outcomes,
                'prerequisites' => $path->prerequisites,
                'joined_at' => $path->pivot->created_at,
                'target_completion_date' => $path->pivot->target_completion_date,
            ];
        });

        return response()->json($userPaths);
    }

    public function join(Request $request, CareerPath $careerPath)
    {
        $user = Auth::user();
        
        // Check if user is already enrolled
        if ($user->careerPaths()->where('career_path_id', $careerPath->id)->exists()) {
            return response()->json([
                'message' => 'You are already enrolled in this career path'
            ], 400);
        }

        // Join the career path
        $userPath = $user->careerPaths()->attach($careerPath->id, [
            'target_completion_date' => $request->input('target_completion_date'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Successfully joined the career path',
            'career_path' => [
                'id' => $careerPath->id,
                'title' => $careerPath->title,
                'joined_at' => now(),
                'target_completion_date' => $request->input('target_completion_date'),
            ]
        ]);
    }

    public function leave(CareerPath $careerPath)
    {
        $user = Auth::user();
        
        // Check if user is enrolled
        if (!$user->careerPaths()->where('career_path_id', $careerPath->id)->exists()) {
            return response()->json([
                'message' => 'You are not enrolled in this career path'
            ], 400);
        }

        // Leave the career path
        $user->careerPaths()->detach($careerPath->id);

        return response()->json([
            'message' => 'Successfully left the career path'
        ]);
    }
}
