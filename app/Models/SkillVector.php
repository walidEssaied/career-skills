<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkillVector extends Model
{
    protected $fillable = ['skill_id', 'vector_data'];

    protected $casts = [
        'vector_data' => 'array',
    ];

    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }
}
