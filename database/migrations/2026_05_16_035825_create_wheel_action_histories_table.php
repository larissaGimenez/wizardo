<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wheel_action_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wheel_id')->constrained()->onDelete('cascade');
            $table->morphs('actionable'); // Spell ou Quest
            $table->integer('points'); // Ganho (+) ou Dano (-)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wheel_action_histories');
    }
};
