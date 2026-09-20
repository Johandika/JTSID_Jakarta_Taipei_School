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
        foreach (['staff_profiles', 'slideshow_items', 'announcement_popups'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->dropColumn('school_levels');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (['staff_profiles', 'slideshow_items', 'announcement_popups'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->json('school_levels')->nullable();
            });
        }
    }
};
