<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseRecommendation extends Model
{
    protected $fillable = ['user_id', 'course_id', 'similarity_score', 'is_viewed'];

    protected $casts = [
        'similarity_score' => 'float',
        'is_viewed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
