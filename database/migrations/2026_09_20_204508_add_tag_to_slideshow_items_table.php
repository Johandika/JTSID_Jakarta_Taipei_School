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
        if (! Schema::hasTable('slideshow_items')) {
            return;
        }

        Schema::table('slideshow_items', function (Blueprint $table) {
            $table->string('tag', 50)->nullable()->after('topic');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('slideshow_items')) {
            return;
        }

        Schema::table('slideshow_items', function (Blueprint $table) {
            $table->dropColumn('tag');
        });
    }
};
