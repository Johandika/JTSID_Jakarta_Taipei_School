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
        if (Schema::hasColumn('mandarin_programs', 'sort_order')) {
            Schema::table('mandarin_programs', function (Blueprint $table) {
                $table->dropColumn('sort_order');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('mandarin_programs', 'sort_order')) {
            Schema::table('mandarin_programs', function (Blueprint $table) {
                $table->unsignedInteger('sort_order')->default(0);
            });
        }
    }
};
