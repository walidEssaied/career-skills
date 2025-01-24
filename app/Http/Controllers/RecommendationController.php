<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseRecommendation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecommendationController extends Controller
{
    public function getCourseRecommendations()
    {
        $user = Auth::user();
        $userSkills = $user->skills->pluck('name')->toArray();
        
        // Get recommendations from ML service
        $recommendations = app('ml.recommender')->recommendCourses($userSkills);
        
        if (empty($recommendations) || isset($recommendations['error'])) {
            return response()->json([
                'message' => 'Unable to generate recommendations at this time',
                'recommendations' => []
            ], 200);
        }

        // Get recommended courses
        $recommendedCourses = [];
        foreach ($recommendations['recommendations'] as $index => $courseId) {
            $course = Course::find($courseId);
            if ($course) {
                // Save recommendation
                CourseRecommendation::updateOrCreate(
                    ['user_id' => $user->id, 'course_id' => $courseId],
                    ['similarity_score' => $recommendations['scores'][$index]]
                );

                $recommendedCourses[] = [
                    'course' => $course,
                    'similarity_score' => $recommendations['scores'][$index]
                ];
            }
        }

        return response()->json([
            'recommendations' => $recommendedCourses
        ]);
    }

    public function getCareerPathPredictions()
    {
        $user = Auth::user();
        $userProfile = [
            'skills' => $user->skills->pluck('name')->toArray(),
            'current_role' => $user->profile->current_role ?? '',
            'experience_years' => $user->profile->experience_years ?? 0
        ];

        $predictions = app('ml.recommender')->predictCareerPath($userProfile);

        return response()->json([
            'predictions' => $predictions
        ]);
    }

    public function analyzeSkillGaps(Request $request)
    {
        $request->validate([
            'target_role' => 'required|string'
        ]);

        $user = Auth::user();
        $userSkills = $user->skills->pluck('name')->toArray();
        
        // Get required skills for target role (you'll need to implement this logic)
        $targetRoleSkills = $this->getRequiredSkillsForRole($request->target_role);
        
        $analysis = app('ml.recommender')->analyzeSkillGaps($userSkills, $targetRoleSkills);

        return response()->json($analysis);
    }

    private function getRequiredSkillsForRole($role)
    {
        // Implement logic to get required skills for a role
        // This could be from a database table or external API
        return []; // Placeholder
    }
}
