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
        Schema::create('articles', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('description');
        $table->string('type');
        $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
        $table->foreignId('created_by_id')->constrained('users');
        $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete();
        $table->boolean('is_featured')->default(false);
        $table->boolean('is_active')->default(true);
        $table->string('status')->default('draft');
        $table->foreignId('featured_image_id')->nullable()->constrained('files')->nullOnDelete();
        $table->dateTime('event_date')->nullable();
        $table->string('event_location')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
