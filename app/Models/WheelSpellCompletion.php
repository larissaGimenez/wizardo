<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WheelSpellCompletion extends Model
{
    protected $fillable = [
        'wheel_id',
        'spell_id',
        'last_completed_at',
        'last_penalty_applied_at'
    ];

    protected $casts = [
        'last_completed_at' => 'date',
        'last_penalty_applied_at' => 'date',
    ];
}
