<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CareerTransition extends Model
{
    protected $fillable = ['user_id', 'previous_role', 'new_role', 'skills_gained'];

    protected $casts = [
        'skills_gained' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
