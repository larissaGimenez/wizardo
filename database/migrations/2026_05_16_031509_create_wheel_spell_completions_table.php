<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wheel_spell_completions', function (Blueprint $create) {
            $create->id();
            $create->foreignId('wheel_id')->constrained()->onDelete('cascade');
            $create->foreignId('spell_id')->constrained()->onDelete('cascade');
            $create->date('last_completed_at'); // Only need the date part
            $create->date('last_penalty_applied_at')->nullable();
            $create->timestamps();

            $create->unique(['wheel_id', 'spell_id', 'last_completed_at'], 'wheel_spell_date_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wheel_spell_completions');
    }
};
