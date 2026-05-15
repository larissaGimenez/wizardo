<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spells', function (Blueprint $table) {
            // Torna a coluna nula
            $table->foreignId('wheel_id')->nullable()->change();
            
            // Derruba a chave estrangeira antiga (que era cascade)
            $table->dropForeign(['wheel_id']);
            
            // Cria a nova chave estrangeira com 'set null'
            $table->foreign('wheel_id')->references('id')->on('wheels')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('spells', function (Blueprint $table) {
            $table->dropForeign(['wheel_id']);
            $table->foreignId('wheel_id')->nullable(false)->change();
            $table->foreign('wheel_id')->references('id')->on('wheels')->onDelete('cascade');
        });
    }
};
