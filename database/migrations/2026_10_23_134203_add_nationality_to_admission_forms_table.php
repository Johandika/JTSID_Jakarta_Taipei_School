<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admission_forms', function (Blueprint $table) {
            $table->string('nationality')->default('wni')->after('school_level');
            $table->unique(['school_level', 'nationality']);
        });
    }

    public function down(): void
    {
        Schema::table('admission_forms', function (Blueprint $table) {
            $table->dropUnique(['school_level', 'nationality']);
            $table->dropColumn('nationality');
        });
    }
};
