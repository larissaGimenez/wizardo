<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Storage;

class Challenge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'level',
        'prize_name',
        'prize_description',
        'wheel_id',
        'is_completed',
    ];

    public function wheel()
    {
        return $this->belongsTo(Wheel::class);
    }

    /**
     * Get the challenge's image.
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
        static::deleting(function ($challenge) {
            if ($challenge->image) {
                // Apaga o arquivo físico
                Storage::disk('public')->delete($challenge->image->path);
                // Apaga o registro no banco
                $challenge->image->delete();
            }
        });
    }
}
