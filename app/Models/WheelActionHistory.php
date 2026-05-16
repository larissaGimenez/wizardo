<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WheelActionHistory extends Model
{
    protected $fillable = [
        'wheel_id',
        'actionable_id',
        'actionable_type',
        'points',
    ];

    public function wheel(): BelongsTo
    {
        return $this->belongsTo(Wheel::class);
    }

    public function actionable(): MorphTo
    {
        return $this->morphTo();
    }
}
