<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class CareerGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'career_path_id',
        'title',
        'description',
        'target_date',
        'status',
        'progress',
        'priority',
        'notes',
    ];

    protected $casts = [
        'target_date' => 'datetime',
        'progress' => 'integer',
        'priority' => 'integer',
    ];

    const STATUS_NOT_STARTED = 'not_started';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_ON_HOLD = 'on_hold';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function careerPath()
    {
        return $this->belongsTo(CareerPath::class);
    }

    /**
     * Calculate and update the progress based on required skills
     */
    public function updateProgress()
    {
        if (!$this->careerPath) {
            return;
        }

        $requiredSkills = $this->careerPath->skills;
        if ($requiredSkills->isEmpty()) {
            $this->update(['progress' => 0]);
            return;
        }

        $userSkills = $this->user->skills;
        $totalProgress = 0;
        $totalWeight = 0;

        foreach ($requiredSkills as $requiredSkill) {
            $weight = $requiredSkill->pivot->importance_level ?? 1;
            $totalWeight += $weight;

            $userSkill = $userSkills->firstWhere('id', $requiredSkill->id);
            if ($userSkill) {
                $skillProgress = ($userSkill->pivot->proficiency_level / 5) * 100;
                $totalProgress += $skillProgress * $weight;
            }
        }

        $overallProgress = $totalWeight > 0 ? round($totalProgress / $totalWeight) : 0;
        
        // Update status based on progress
        $status = $this->determineStatus($overallProgress);
        
        $this->update([
            'progress' => $overallProgress,
            'status' => $status
        ]);
    }

    /**
     * Determine the status based on progress and target date
     */
    private function determineStatus($progress)
    {
        if ($progress >= 100) {
            return self::STATUS_COMPLETED;
        }

        if ($progress === 0) {
            return self::STATUS_NOT_STARTED;
        }

        if ($this->status === self::STATUS_ON_HOLD) {
            return self::STATUS_ON_HOLD;
        }

        return self::STATUS_IN_PROGRESS;
    }

    /**
     * Check if the goal is overdue
     */
    public function isOverdue()
    {
        return $this->target_date && $this->target_date < Carbon::now() && 
               $this->status !== self::STATUS_COMPLETED;
    }

    /**
     * Get the estimated completion date based on current progress
     */
    public function getEstimatedCompletionDate()
    {
        if ($this->status === self::STATUS_COMPLETED) {
            return null;
        }

        if ($this->progress === 0 || !$this->created_at || !$this->target_date) {
            return $this->target_date;
        }

        $daysSinceStart = Carbon::now()->diffInDays($this->created_at);
        $progressPerDay = $this->progress / $daysSinceStart;

        if ($progressPerDay <= 0) {
            return $this->target_date;
        }

        $daysToCompletion = round((100 - $this->progress) / $progressPerDay);
        return Carbon::now()->addDays($daysToCompletion);
    }

    /**
     * Get required skills that are not yet achieved
     */
    public function getMissingSkills()
    {
        if (!$this->careerPath) {
            return collect();
        }

        $requiredSkills = $this->careerPath->skills;
        $userSkills = $this->user->skills->keyBy('id');

        return $requiredSkills->filter(function ($skill) use ($userSkills) {
            $userSkill = $userSkills->get($skill->id);
            return !$userSkill || $userSkill->pivot->proficiency_level < ($skill->pivot->required_level ?? 3);
        });
    }

    /**
     * Get the next recommended course for this career goal
     */
    public function getNextRecommendedCourse()
    {
        $missingSkills = $this->getMissingSkills()->pluck('id');
        $enrolledCourses = $this->user->courses->pluck('id');

        return Course::whereHas('skills', function ($query) use ($missingSkills) {
                $query->whereIn('skills.id', $missingSkills);
            })
            ->whereNotIn('id', $enrolledCourses)
            ->orderBy('rating', 'desc')
            ->first();
    }
}
