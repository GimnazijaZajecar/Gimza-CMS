<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ставка менија води или на страницу из CMS-а (page_id) или на произвољан линк (url).
     * Колона menu говори у који мени ставка иде (App\Enums\MenuLocation).
     */
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('menu')->default('glavni');
            $table->string('label');
            $table->foreignId('page_id')->nullable()->constrained('pages')->nullOnDelete();
            $table->string('url')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->nullOnDelete();
            $table->integer('order')->default(0);
            $table->boolean('is_external')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['menu', 'parent_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
