<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model
{
    protected $fillable = ['path'];

    /**
     * Get the parent imageable model (Spell, Wheel, etc.).
     */
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
