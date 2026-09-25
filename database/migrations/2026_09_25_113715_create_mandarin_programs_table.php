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
        Schema::create('mandarin_programs', function (Blueprint $table) {
            $table->id();
            $table->string('category')->index(); // 'mandarin_class', 'tocfl', 'workshop_event', etc.
            $table->jsonb('title');
            $table->string('slug')->nullable()->index();
            $table->jsonb('short_description')->nullable();
            $table->jsonb('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->jsonb('gallery_images')->nullable();
            $table->jsonb('program_date_label')->nullable();
            $table->date('program_start_date')->nullable();
            $table->date('program_end_date')->nullable();
            $table->jsonb('program_date_text')->nullable();
            $table->date('registration_start_date')->nullable();
            $table->date('registration_end_date')->nullable();
            $table->jsonb('registration_date_text')->nullable();
            $table->jsonb('location')->nullable();
            $table->string('registration_url')->nullable();
            $table->jsonb('contact_info')->nullable();
            $table->jsonb('extra_info_title')->nullable();
            $table->jsonb('extra_info_items')->nullable();
            $table->string('status_override')->default('auto'); // auto, upcoming, registration_open, registration_closed, program_done
            $table->boolean('show_details')->default(true);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mandarin_programs');
    }
};
