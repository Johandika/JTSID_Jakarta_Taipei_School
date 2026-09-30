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
        Schema::table('subject_resources', function (Blueprint $table) {
            $table->string('class_level', 20)->nullable()->after('school_levels');
            $table->string('semester', 20)->nullable()->after('class_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subject_resources', function (Blueprint $table) {
            $table->dropColumn(['class_level', 'semester']);
        });
    }
};
