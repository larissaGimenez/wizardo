<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Storage;

class Quest extends Model
{
    protected $fillable = [
        'wheel_id',
        'name',
        'description',
        'gain',
    ];

    public function wheel(): BelongsTo
    {
        return $this->belongsTo(Wheel::class);
    }

    /**
     * Get the quest's image.
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
        static::deleting(function ($quest) {
            if ($quest->image) {
                // Apaga o arquivo físico
                Storage::disk('public')->delete($quest->image->path);
                // Apaga o registro no banco
                $quest->image->delete();
            }
        });
    }
}
