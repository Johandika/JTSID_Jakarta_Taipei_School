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
        Schema::table('hero_slides', function (Blueprint $table) {
            $table->string('image')->nullable()->change();
            $table->json('title')->nullable()->change();
            $table->boolean('show_text')->default(true)->after('cta_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hero_slides', function (Blueprint $table) {
            $table->dropColumn('show_text');
            $table->string('image')->nullable(false)->change();
            $table->json('title')->nullable(false)->change();
        });
    }
};
