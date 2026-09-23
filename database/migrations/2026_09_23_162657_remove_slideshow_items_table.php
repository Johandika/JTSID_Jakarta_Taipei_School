<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('audit_logs')
            ->where('entity_type', 'SlideshowItem')
            ->delete();

        Schema::dropIfExists('slideshow_items');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('slideshow_items', function (Blueprint $table): void {
            $table->id();
            $table->string('topic')->unique();
            $table->string('image');
            $table->string('link_url')->nullable();
            $table->string('tag', 50)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
};
