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
            ->where('entity_type', 'ContactChannel')
            ->delete();

        Schema::dropIfExists('contact_channels');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('contact_channels', function (Blueprint $table) {
            $table->id();
            $table->string('channel')->unique();
            $table->string('label');
            $table->string('value');
            $table->string('qr_image')->nullable();
            $table->boolean('is_visible_header')->default(false);
            $table->boolean('is_visible_footer')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }
};
