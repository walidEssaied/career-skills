<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCourse extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'status',
        'progress',
        'rating',
        'completion_date'
    ];

    protected $casts = [
        'progress' => 'integer',
        'rating' => 'integer',
        'completion_date' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
