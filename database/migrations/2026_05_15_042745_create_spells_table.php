<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spells', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wheel_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('action')->nullable();
            $table->integer('gain')->default(0);
            $table->integer('damage')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spells');
    }
};
