<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSkill extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'skill_id',
        'proficiency_level',
        'target_level',
        'last_practiced_at',
        'verified',
        'verification_method',
        'endorsements_count',
    ];

    protected $casts = [
        'last_practiced_at' => 'datetime',
        'verified' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }

    public function endorsements()
    {
        return $this->hasMany(SkillEndorsement::class);
    }
}
