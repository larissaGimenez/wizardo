<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LevelTitle extends Model
{
    protected $fillable = ['level', 'title', 'wheel_id'];

    public function wheel()
    {
        return $this->belongsTo(Wheel::class);
    }
}
