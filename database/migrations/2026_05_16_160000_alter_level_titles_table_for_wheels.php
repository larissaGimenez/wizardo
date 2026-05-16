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
        Schema::table('level_titles', function (Blueprint $table) {
            $table->dropUnique('level_titles_level_unique');
            $table->foreignId('wheel_id')->nullable()->constrained()->onDelete('cascade');
            $table->unique(['wheel_id', 'level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('level_titles', function (Blueprint $table) {
            $table->dropUnique(['wheel_id', 'level']);
            $table->dropForeign(['wheel_id']);
            $table->dropColumn('wheel_id');
            $table->unique('level');
        });
    }
};
