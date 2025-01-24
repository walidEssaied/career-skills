<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CareerPath extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'required_experience_years',
        'salary_range_min',
        'salary_range_max',
        'growth_potential',
        'market_demand',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'career_path_skills')
            ->withPivot('importance_level')
            ->withTimestamps();
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'career_path_courses')
            ->withPivot('order', 'required')
            ->withTimestamps();
    }

    public function careerGoals()
    {
        return $this->hasMany(CareerGoal::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'career_goals')
            ->withPivot(['target_completion_date', 'progress', 'status', 'notes'])
            ->withTimestamps();
    }
}
