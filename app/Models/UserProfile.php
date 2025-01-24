<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'current_position',
        'industry_sector',
        'years_of_experience',
        'education_level',
        'bio',
        'location',
        'profile_picture',
        'linkedin_url',
        'github_url',
        'portfolio_url',
        'resume_path',
        'preferences',
    ];

    protected $casts = [
        'preferences' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
