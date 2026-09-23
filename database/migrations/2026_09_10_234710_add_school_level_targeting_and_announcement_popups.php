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
        foreach (['posts', 'subject_resources', 'slideshow_items', 'staff_profiles'] as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            Schema::table($table, fn (Blueprint $table) => $table->json('school_levels')->nullable());
        }
        Schema::table('subject_resources', fn (Blueprint $table) => $table->unsignedBigInteger('grade_level_id')->nullable()->change());
        foreach (DB::table('grade_levels')->get() as $grade) {
            DB::table('subject_resources')->where('grade_level_id', $grade->id)
                ->update(['school_levels' => json_encode([$grade->code])]);
        }
        DB::table('post_categories')->insertOrIgnore(['code' => 'announcement', 'name' => 'Announcements', 'created_at' => now(), 'updated_at' => now()]);

        Schema::create('announcement_popups', function (Blueprint $table): void {
            $table->id();
            $table->string('title', 191);
            $table->text('body')->nullable();
            $table->string('image')->nullable();
            $table->string('link_url')->nullable();
            $table->json('school_levels')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['is_active', 'starts_at', 'ends_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcement_popups');
        foreach (['posts', 'subject_resources', 'slideshow_items', 'staff_profiles'] as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            Schema::table($table, fn (Blueprint $table) => $table->dropColumn('school_levels'));
        }
    }
};
