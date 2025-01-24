<?php

namespace App\Services;

use App\Models\User;
use App\Models\Skill;
use App\Models\Course;
use App\Models\CareerPath;
use Illuminate\Support\Facades\Cache;
use Phpml\Classification\SVC;
use Phpml\FeatureExtraction\TfIdfTransformer;
use Phpml\Tokenization\WhitespaceTokenizer;

class RecommendationService
{
    protected $tfIdf;
    protected $classifier;

    public function __construct()
    {
        $this->tfIdf = new TfIdfTransformer();
        $this->classifier = new SVC();
    }

    public function recommendSkills(User $user, $limit = 5)
    {
        $cacheKey = "skill_recommendations_{$user->id}";
        
        return Cache::remember($cacheKey, now()->addHours(24), function () use ($user, $limit) {
            // Get user's current skills
            $userSkills = $user->skills()->with('skill')->get();
            $userSkillIds = $userSkills->pluck('skill_id')->toArray();
            
            // Get user's career goals
            $careerGoals = $user->careerGoals()->with('careerPath.skills')->get();
            
            // Calculate skill scores
            $skillScores = [];
            $allSkills = Skill::whereNotIn('id', $userSkillIds)->get();
            
            foreach ($allSkills as $skill) {
                $score = 0;
                
                // Score based on similarity to current skills
                foreach ($userSkills as $userSkill) {
                    $similarity = $this->calculateSkillSimilarity($skill, $userSkill->skill);
                    $score += $similarity * 0.4; // 40% weight for skill similarity
                }
                
                // Score based on career goals
                foreach ($careerGoals as $goal) {
                    if ($goal->careerPath->skills->contains($skill)) {
                        $importance = $goal->careerPath->skills->find($skill->id)->pivot->importance_level;
                        $score += $importance * 0.6; // 60% weight for career relevance
                    }
                }
                
                $skillScores[$skill->id] = $score;
            }
            
            // Sort skills by score and get top recommendations
            arsort($skillScores);
            $recommendedSkillIds = array_slice(array_keys($skillScores), 0, $limit);
            
            return Skill::whereIn('id', $recommendedSkillIds)->get();
        });
    }

    public function recommendCourses(User $user, $limit = 5)
    {
        $cacheKey = "course_recommendations_{$user->id}";
        
        return Cache::remember($cacheKey, now()->addHours(24), function () use ($user, $limit) {
            $userSkills = $user->skills()->with('skill')->get();
            $careerGoals = $user->careerGoals()->with('careerPath')->get();
            
            // Get courses not yet taken by the user
            $takenCourseIds = $user->courseEnrollments()->pluck('course_id')->toArray();
            $availableCourses = Course::whereNotIn('id', $takenCourseIds)->with('skills')->get();
            
            $courseScores = [];
            foreach ($availableCourses as $course) {
                $score = 0;
                
                // Score based on skill relevance
                foreach ($userSkills as $userSkill) {
                    if ($course->skills->contains($userSkill->skill_id)) {
                        $skillLevel = $course->skills->find($userSkill->skill_id)->pivot->skill_level_gained;
                        $score += ($userSkill->target_level - $userSkill->proficiency_level) * $skillLevel;
                    }
                }
                
                // Score based on career path alignment
                foreach ($careerGoals as $goal) {
                    if ($goal->careerPath->courses->contains($course)) {
                        $required = $goal->careerPath->courses->find($course->id)->pivot->required;
                        $score += $required ? 5 : 3;
                    }
                }
                
                $courseScores[$course->id] = $score;
            }
            
            arsort($courseScores);
            $recommendedCourseIds = array_slice(array_keys($courseScores), 0, $limit);
            
            return Course::whereIn('id', $recommendedCourseIds)->get();
        });
    }

    public function recommendCareerPaths(User $user, $limit = 5)
    {
        $cacheKey = "career_path_recommendations_{$user->id}";
        
        return Cache::remember($cacheKey, now()->addHours(24), function () use ($user, $limit) {
            $userSkills = $user->skills()->with('skill')->get();
            $userProfile = $user->profile;
            
            $careerPaths = CareerPath::with('skills')->get();
            $careerPathScores = [];
            
            foreach ($careerPaths as $careerPath) {
                $score = 0;
                
                // Score based on skill match
                $requiredSkills = $careerPath->skills;
                $matchedSkills = 0;
                foreach ($requiredSkills as $requiredSkill) {
                    $userSkill = $userSkills->firstWhere('skill_id', $requiredSkill->id);
                    if ($userSkill) {
                        $matchedSkills++;
                        $score += ($userSkill->proficiency_level / 5) * $requiredSkill->pivot->importance_level;
                    }
                }
                
                // Adjust score based on skill coverage
                $skillCoverage = $matchedSkills / $requiredSkills->count();
                $score *= $skillCoverage;
                
                // Adjust score based on experience match
                if ($userProfile->years_of_experience >= $careerPath->required_experience) {
                    $score *= 1.2;
                }
                
                // Adjust score based on industry match
                if ($userProfile->industry_sector === $careerPath->industry) {
                    $score *= 1.3;
                }
                
                $careerPathScores[$careerPath->id] = $score;
            }
            
            arsort($careerPathScores);
            $recommendedCareerPathIds = array_slice(array_keys($careerPathScores), 0, $limit);
            
            return CareerPath::whereIn('id', $recommendedCareerPathIds)->get();
        });
    }

    protected function calculateSkillSimilarity(Skill $skill1, Skill $skill2)
    {
        // Combine name and description for comparison
        $text1 = $skill1->name . ' ' . $skill1->description;
        $text2 = $skill2->name . ' ' . $skill2->description;
        
        // Tokenize and calculate TF-IDF
        $tokenizer = new WhitespaceTokenizer();
        $documents = [
            $tokenizer->tokenize($text1),
            $tokenizer->tokenize($text2)
        ];
        
        $vectors = $this->tfIdf->transform($documents);
        
        // Calculate cosine similarity
        return $this->cosineSimilarity($vectors[0], $vectors[1]);
    }

    protected function cosineSimilarity(array $vector1, array $vector2)
    {
        $dotProduct = 0;
        $magnitude1 = 0;
        $magnitude2 = 0;
        
        foreach ($vector1 as $key => $value) {
            $dotProduct += $value * ($vector2[$key] ?? 0);
            $magnitude1 += $value * $value;
            $magnitude2 += ($vector2[$key] ?? 0) * ($vector2[$key] ?? 0);
        }
        
        $magnitude1 = sqrt($magnitude1);
        $magnitude2 = sqrt($magnitude2);
        
        return $magnitude1 && $magnitude2 ? $dotProduct / ($magnitude1 * $magnitude2) : 0;
    }
}
