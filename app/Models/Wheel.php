<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Storage;

class Wheel extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function spells(): HasMany
    {
        return $this->hasMany(Spell::class);
    }

    public function challenges(): HasMany
    {
        return $this->hasMany(Challenge::class);
    }

    public function quests(): HasMany
    {
        return $this->hasMany(Quest::class);
    }

    /**
     * Get the wheel's image.
     */
    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    /**
     * Boot the model.
     */
    protected static function booted()
    {
        static::deleting(function ($wheel) {
            if ($wheel->image) {
                // Apaga o arquivo físico
                Storage::disk('public')->delete($wheel->image->path);
                // Apaga o registro no banco
                $wheel->image->delete();
            }
        });
    }
}
