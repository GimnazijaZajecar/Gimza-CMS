<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Који чланци се приказују на којој страници и којим редом.
     */
    public function up(): void
    {
        Schema::create('article_page', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('pages')->cascadeOnDelete();
            $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->unique(['page_id', 'article_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_page');
    }
};
