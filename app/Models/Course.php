<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'currency',
        'rating',
        'reviews_count',
        'language',
        'certificate_offered',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'certificate_offered' => 'boolean',
        'price' => 'float',
    ];

    /**
     * Get the users enrolled in this course.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_courses')
                    ->withPivot(['progress', 'status', 'rating'])
                    ->withTimestamps();
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'course_skill');
    }

    public function userCourses(): HasMany
    {
        return $this->hasMany(UserCourse::class);
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
