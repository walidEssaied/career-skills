<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Skill;
use App\Models\Course;
use App\Models\CareerGoal;
use App\Models\CareerPath;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
    ];

    /**
     * Check if the user is an administrator.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * Get the user's skills.
     */
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'user_skills')
            ->withPivot('proficiency_level', 'target_level', 'last_practiced_at', 'verified', 'verification_method', 'endorsements_count')
            ->withTimestamps();
    }

    /**
     * Get the user's career goals.
     */
    public function careerGoals()
    {
        return $this->hasMany(CareerGoal::class);
    }

    /**
     * Get the user's courses.
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'user_courses')
            ->withPivot('status', 'progress', 'completion_date')
            ->withTimestamps();
    }

    /**
     * Get the user's active career paths through their goals.
     */
    public function careerPaths()
    {
        return $this->belongsToMany(CareerPath::class, 'career_goals')
            ->withPivot('status', 'progress', 'target_completion_date', 'notes')
            ->withTimestamps();
    }

    /**
     * Get skills that need attention (not practiced recently or below target level)
     */
    public function getSkillsNeedingAttention()
    {
        $threshold = Carbon::now()->subDays(30); // Skills not practiced in 30 days
        
        return $this->skills()
            ->where(function ($query) use ($threshold) {
                $query->whereNull('user_skills.last_practiced_at')
                    ->orWhere('user_skills.last_practiced_at', '<', $threshold)
                    ->orWhereRaw('user_skills.proficiency_level < user_skills.target_level');
            })
            ->get();
    }

    /**
     * Get the overall progress for a specific career path
     */
    public function getCareerPathProgress(CareerPath $careerPath)
    {
        $requiredSkills = $careerPath->skills;
        if ($requiredSkills->isEmpty()) {
            return 0;
        }

        $totalProgress = 0;
        $userSkills = $this->skills;

        foreach ($requiredSkills as $requiredSkill) {
            $userSkill = $userSkills->firstWhere('id', $requiredSkill->id);
            if ($userSkill) {
                $totalProgress += ($userSkill->pivot->proficiency_level / 5) * 100;
            }
        }

        return round($totalProgress / $requiredSkills->count());
    }

    /**
     * Get recommended courses based on career goals and current skill levels
     */
    public function getRecommendedCourses()
    {
        $userSkills = $this->skills->pluck('pivot.proficiency_level', 'id');
        $enrolledCourses = $this->courses->pluck('id');
        
        return Course::whereHas('skills', function ($query) use ($userSkills) {
                $query->whereIn('skills.id', array_keys($userSkills->toArray()))
                    ->whereRaw('skill_level_gained > ?', [3]); // Courses that offer advanced level
            })
            ->whereNotIn('id', $enrolledCourses)
            ->orderBy('rating', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Check if all required skills for a career path are at target level
     */
    public function hasAchievedCareerPathSkills(CareerPath $careerPath)
    {
        $requiredSkills = $careerPath->skills;
        $userSkills = $this->skills;

        foreach ($requiredSkills as $requiredSkill) {
            $userSkill = $userSkills->firstWhere('id', $requiredSkill->id);
            if (!$userSkill || $userSkill->pivot->proficiency_level < $requiredSkill->pivot->required_level) {
                return false;
            }
        }

        return true;
    }

    /**
     * Update skill proficiency and related timestamps
     */
    public function updateSkillProficiency(Skill $skill, int $newLevel)
    {
        $this->skills()->updateExistingPivot($skill->id, [
            'proficiency_level' => $newLevel,
            'last_practiced_at' => Carbon::now(),
        ]);

        // Update related career goals progress
        $this->careerGoals()
            ->whereHas('careerPath.skills', function ($query) use ($skill) {
                $query->where('skills.id', $skill->id);
            })
            ->each(function ($goal) {
                $goal->updateProgress();
            });
    }

    /**
     * Get the next recommended skill to focus on
     */
    public function getNextRecommendedSkill()
    {
        return $this->skills()
            ->whereRaw('proficiency_level < target_level')
            ->orderByRaw('(target_level - proficiency_level) DESC')
            ->first();
    }
}
