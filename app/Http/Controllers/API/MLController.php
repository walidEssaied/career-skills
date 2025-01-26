<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\CareerPath;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MLController extends Controller
{
    /**
     * Get course recommendations for the authenticated user
     */
    public function getRecommendations()
    {
        $user = Auth::user();
        
        // Get user's current skills with proper column selection
        $userSkills = $user->skills()
            ->select(['skills.id', 'user_skills.proficiency_level'])
            ->pluck('proficiency_level', 'skills.id')
            ->toArray();
            
        // Get user's enrolled courses with explicit column selection
        $userCourses = $user->courses()
            ->select('courses.id as course_id')
            ->pluck('course_id')
            ->toArray();
        
        // Get courses not taken by the user with their skills
        $availableCourses = Course::with(['skills' => function($query) {
            $query->select('skills.*', 'course_skill.skill_level_gained');
        }])
        ->whereNotIn('id', $userCourses)
        ->get();
        
        // Simple recommendation algorithm based on skill matching
        $recommendations = $availableCourses->map(function ($course) use ($userSkills) {
            $courseSkills = $course->skills->pluck('pivot.skill_level_gained', 'id')->toArray();
            $matchScore = $this->calculateMatchScore($courseSkills, $userSkills);
            
            return [
                'id' => $course->id,
                'title' => $course->title,
                'description' => $course->description,
                'provider' => $course->provider,
                'duration' => $course->duration,
                'difficulty_level' => $course->difficulty_level,
                'match_score' => $matchScore,
                'skills' => $course->skills->map(function ($skill) {
                    return [
                        'id' => $skill->id,
                        'name' => $skill->name,
                        'level_gained' => $skill->pivot->skill_level_gained,
                    ];
                }),
            ];
        })->sortByDesc('match_score')->values()->take(5);
        
        return response()->json($recommendations);
    }
    
    /**
     * Predict career paths suitable for the user
     */
    public function predictCareerPath()
    {
        $user = Auth::user();
        $userSkills = $user->skills()->select(['skills.*', 'user_skills.proficiency_level'])->get();
        $careerPaths = CareerPath::with(['skills' => function ($query) {
            $query->select(['skills.*', 'career_path_skills.importance_level']);
        }])->get();
        
        $predictions = $careerPaths->map(function ($path) use ($userSkills) {
            $matchingSkills = $path->skills->filter(function ($pathSkill) use ($userSkills) {
                return $userSkills->contains('id', $pathSkill->id);
            });
            
            $totalSkills = $path->skills->count() ?: 1;
            $skillScore = $matchingSkills->sum(function ($skill) use ($userSkills) {
                $userSkill = $userSkills->firstWhere('id', $skill->id);
                $userLevel = $userSkill ? $userSkill->proficiency_level : 0;
                $requiredLevel = $skill->pivot->importance_level;
                return ($userLevel / $requiredLevel) * 100;
            });
            
            $confidence = $skillScore / $totalSkills;
            
            return [
                'path_id' => $path->id,
                'title' => $path->title,
                'description' => $path->description,
                'confidence' => round($confidence, 2),
                'matching_skills' => $matchingSkills->map(function ($skill) use ($userSkills) {
                    $userSkill = $userSkills->firstWhere('id', $skill->id);
                    return [
                        'id' => $skill->id,
                        'name' => $skill->name,
                        'current_level' => $userSkill ? $userSkill->proficiency_level : 0,
                        'required_level' => $skill->pivot->importance_level,
                    ];
                }),
            ];
        })->sortByDesc('confidence')->values()->take(3);
        
        return response()->json($predictions);
    }
    
    /**
     * Analyze skill gaps for a specific career path
     */
    public function analyzeSkillGaps(Request $request)
    {
        $request->validate([
            'career_path_id' => 'required|exists:career_paths,id',
        ]);
        
        $user = Auth::user();
        $careerPath = CareerPath::with(['skills' => function ($query) {
            $query->select(['skills.*', 'career_path_skills.importance_level']);
        }])->findOrFail($request->career_path_id);
        
        $userSkills = $user->skills()->get()->pluck('pivot.proficiency_level', 'id')->toArray();
        
        $skillGaps = $careerPath->skills->map(function ($skill) use ($userSkills) {
            $currentLevel = $userSkills[$skill->id] ?? 0;
            $recommendedLevel = $skill->pivot->importance_level;
            
            return [
                'skill_id' => $skill->id,
                'skill_name' => $skill->name,
                'current_level' => $currentLevel,
                'recommended_level' => $recommendedLevel,
                'importance' => $skill->pivot->importance_level,
                'gap' => max(0, $recommendedLevel - $currentLevel)
            ];
        })->sortByDesc('gap')->values();
        
        return response()->json([
            'career_path' => [
                'id' => $careerPath->id,
                'title' => $careerPath->title,
            ],
            'skill_gaps' => $skillGaps
        ]);
    }
    
    /**
     * Calculate match score between course skills and user's skills
     */
    private function calculateMatchScore($courseSkills, $userSkills)
    {
        if (empty($courseSkills)) {
            return 0;
        }

        $totalScore = 0;
        foreach ($courseSkills as $skillId => $levelGained) {
            $userLevel = $userSkills[$skillId] ?? 0;
            // Higher score if the course teaches skills slightly above user's current level
            $levelDiff = $levelGained - $userLevel;
            if ($levelDiff > 0 && $levelDiff <= 2) {
                $totalScore += 100; // Perfect match - skill level is just right
            } elseif ($levelDiff > 2) {
                $totalScore += 50; // Course might be too advanced
            } else {
                $totalScore += 25; // User might already know this skill well
            }
        }

        return $totalScore / count($courseSkills);
    }
}
