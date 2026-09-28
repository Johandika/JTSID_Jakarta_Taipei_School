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
        Schema::table('admission_forms', function (Blueprint $table) {
            $table->string('school_level')->nullable()->after('title');
        });

        DB::table('admission_forms')
            ->whereNull('school_level')
            ->update(['school_level' => 'tk']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admission_forms', function (Blueprint $table) {
            $table->dropColumn('school_level');
        });
    }
};
