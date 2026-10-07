<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('subject_resources')
            ->where('class_level', 'tk')
            ->update(['class_level' => 'tk1']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('subject_resources')
            ->where('class_level', 'tk1')
            ->update(['class_level' => 'tk']);
    }
};
