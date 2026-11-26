<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Одељења (нпр. III-2). Назив се прави од разреда и броја одељења.
     */
    public function up(): void
    {
        Schema::create('school_classes', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('grade');
            $table->unsignedTinyInteger('section');
            $table->string('program')->nullable();
            $table->timestamps();

            $table->unique(['grade', 'section']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_classes');
    }
};
