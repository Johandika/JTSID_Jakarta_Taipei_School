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
            ->whereIn('entity_type', ['Page', 'PageTranslation'])
            ->delete();

        Schema::dropIfExists('page_translations');
        Schema::dropIfExists('pages');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('template')->default('default');
            $table->string('hero_image')->nullable();
            $table->string('background_image')->nullable();
            $table->string('gallery_url')->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('page_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('title', 191);
            $table->text('summary')->nullable();
            $table->text('body')->nullable();
            $table->string('seo_title', 191)->nullable();
            $table->string('seo_description')->nullable();
            $table->timestamps();
            $table->unique(['page_id', 'locale']);
        });
    }
};
