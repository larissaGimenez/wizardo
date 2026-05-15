<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Storage;

class Spell extends Model
{
    protected $fillable = [
        'wheel_id',
        'name',
        'action',
        'gain',
        'damage',
        'type',
    ];

    public function wheel(): BelongsTo
    {
        return $this->belongsTo(Wheel::class);
    }

    /**
     * Get the spell's image.
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
        static::deleting(function ($spell) {
            if ($spell->image) {
                // Apaga o arquivo físico
                Storage::disk('public')->delete($spell->image->path);
                // Apaga o registro no banco
                $spell->image->delete();
            }
        });
    }
}
