<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CourseRecommendation;
use App\Models\CareerTransition;
use App\Models\SkillVector;
use App\Models\UserSkill;
use App\Services\ML\PythonMLService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MLController extends Controller
{
    protected $mlService;

    public function __construct(PythonMLService $mlService)
    {
        $this->mlService = $mlService;
    }

    public function getCourseRecommendations(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $userSkills = UserSkill::where('user_id', $userId)->with('skill')->get();
        
        $recommendations = $this->mlService->recommendCourses($userSkills);
        
        return response()->json([
            'success' => true,
            'recommendations' => $recommendations
        ]);
    }

    public function predictCareerPath(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $userSkills = UserSkill::where('user_id', $userId)->with('skill')->get();
        
        $predictions = $this->mlService->predictCareerPath($userSkills);
        
        return response()->json([
            'success' => true,
            'career_predictions' => $predictions
        ]);
    }

    public function analyzeSkillGaps(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $targetRole = $request->input('target_role');
        
        $userSkills = UserSkill::where('user_id', $userId)->with('skill')->get();
        $gaps = $this->mlService->analyzeSkillGaps($userSkills, $targetRole);
        
        return response()->json([
            'success' => true,
            'skill_gaps' => $gaps
        ]);
    }

    public function updateSkillVectors(): JsonResponse
    {
        $result = $this->mlService->updateSkillVectors();
        
        return response()->json([
            'success' => true,
            'message' => 'Skill vectors updated successfully',
            'details' => $result
        ]);
    }
}
