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
        Schema::create('admission_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_level_id')->constrained('grade_levels')->cascadeOnDelete();
            $table->jsonb('name');
            $table->string('amount');
            $table->jsonb('payment_period');
            $table->jsonb('note')->nullable();
            $table->string('icon')->default('file-text');
            $table->string('icon_color')->default('green');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_fees');
    }
};
