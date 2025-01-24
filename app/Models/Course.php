<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'provider',
        'url',
        'duration',
        'difficulty_level',
        'price',
        'rating',
        'reviews_count',
        'language',
        'certificate_offered',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'certificate_offered' => 'boolean',
    ];

    /**
     * Get the users enrolled in this course.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_enrollments')
                    ->withTimestamps()
                    ->withPivot('completed_at', 'progress');
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'course_skills')
            ->withPivot('skill_level_gained')
            ->withTimestamps();
    }

    public function careerPaths()
    {
        return $this->belongsToMany(CareerPath::class, 'career_path_courses')
            ->withPivot('order', 'required')
            ->withTimestamps();
    }

    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function reviews()
    {
        return $this->hasMany(CourseReview::class);
    }
}
