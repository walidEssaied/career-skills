<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCareerPath extends Model
{
    protected $fillable = [
        'user_id',
        'career_path_id',
        'target_completion_date',
        'completed_at',
    ];

    protected $casts = [
        'target_completion_date' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function careerPath(): BelongsTo
    {
        return $this->belongsTo(CareerPath::class);
    }
}
