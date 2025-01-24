<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category', // technical, soft_skill, language
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function users()
    {
        return $this->hasMany(UserSkill::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_skills');
    }

    public function careerPaths()
    {
        return $this->belongsToMany(CareerPath::class, 'career_path_skills');
    }
}
