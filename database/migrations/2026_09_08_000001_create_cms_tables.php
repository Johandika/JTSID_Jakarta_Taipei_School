<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $t) {
            $t->id();
            $t->string('code')->unique();
            $t->string('name');
            $t->timestamps();
        });
        Schema::create('departments', function (Blueprint $t) {
            $t->id();
            $t->string('code')->unique();
            $t->string('name');
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });
        Schema::table('users', function (Blueprint $t) {
            $t->foreignId('role_id')->nullable()->constrained()->restrictOnDelete();
            $t->foreignId('department_id')->nullable()->constrained()->restrictOnDelete();
            $t->boolean('is_active')->default(true);
            $t->timestamp('last_login_at')->nullable();
        });
        foreach (['post_categories', 'grade_levels', 'subject_resource_types', 'academic_years'] as $table) {
            Schema::create($table, function (Blueprint $t) {
                $t->id();
                $t->string('code')->unique();
                $t->string('name');
                $t->timestamps();
            });
        }
        Schema::create('pages', function (Blueprint $t) {
            $t->id();
            $t->string('slug')->unique();
            $t->string('template')->default('default');
            $t->string('hero_image')->nullable();
            $t->string('background_image')->nullable();
            $t->string('gallery_url')->nullable();
            $this->publishing($t);
            $t->timestamps();
        });
        Schema::create('posts', function (Blueprint $t) {
            $t->id();
            $t->string('slug')->unique();
            $t->string('topic');
            $t->foreignId('department_id')->constrained()->restrictOnDelete();
            $t->foreignId('category_id')->constrained('post_categories')->restrictOnDelete();
            $t->string('image')->nullable();
            $t->string('file_path')->nullable();
            $t->date('upload_date');
            $this->publishing($t);
            $t->timestamps();
            $t->index(['department_id', 'category_id', 'status']);
            $t->index(['upload_date', 'status']);
        });
        Schema::create('subject_resources', function (Blueprint $t) {
            $t->id();
            $t->string('topic');
            $t->foreignId('department_id')->constrained()->restrictOnDelete();
            $t->foreignId('type_id')->constrained('subject_resource_types')->restrictOnDelete();
            $t->foreignId('grade_level_id')->constrained()->restrictOnDelete();
            $t->string('file_path');
            $t->date('upload_date');
            $this->publishing($t);
            $t->timestamps();
            $t->index(['type_id', 'grade_level_id', 'upload_date', 'status'], 'resources_listing_index');
        });
        foreach (['page', 'post', 'subject_resource'] as $entity) {
            Schema::create($entity.'_translations', function (Blueprint $t) use ($entity) {
                $t->id();
                $t->foreignId($entity.'_id')->constrained()->cascadeOnDelete();
                $t->string('locale', 10);
                $t->string('title', 191);
                $t->text('summary')->nullable();
                $t->text('body')->nullable();
                $t->string('seo_title', 191)->nullable();
                $t->string('seo_description')->nullable();
                $t->timestamps();
                $t->unique([$entity.'_id', 'locale']);
            });
        }
        Schema::create('menu_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('parent_id')->nullable()->constrained('menu_items')->cascadeOnDelete();
            $t->string('label');
            $t->string('url');
            $t->string('menu_area')->default('header');
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });
        Schema::create('menu_item_translations', function (Blueprint $t) {
            $t->id();
            $t->foreignId('menu_item_id')->constrained()->cascadeOnDelete();
            $t->string('locale', 10);
            $t->string('title');
            $t->timestamps();
            $t->unique(['menu_item_id', 'locale']);
        });
        Schema::create('slideshow_items', function (Blueprint $t) {
            $t->id();
            $t->string('topic')->unique();
            $t->string('image');
            $t->string('link_url')->nullable();
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });
        Schema::create('contact_channels', function (Blueprint $t) {
            $t->id();
            $t->string('channel')->unique();
            $t->string('label');
            $t->string('value');
            $t->string('qr_image')->nullable();
            $t->boolean('is_visible_header')->default(false);
            $t->boolean('is_visible_footer')->default(true);
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();
        });
        Schema::create('staff_profiles', function (Blueprint $t) {
            $t->id();
            $t->foreignId('department_id')->constrained()->restrictOnDelete();
            $t->string('name');
            $t->string('title');
            $t->string('extension')->nullable();
            $t->text('responsibilities')->nullable();
            $t->string('photo')->nullable();
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });
        Schema::create('university_acceptances', function (Blueprint $t) {
            $t->id();
            $t->foreignId('department_id')->constrained()->restrictOnDelete();
            $t->foreignId('academic_year_id')->constrained()->restrictOnDelete();
            $t->string('student_name');
            $t->string('university_name');
            $t->string('program_name')->nullable();
            $t->string('admission_path')->nullable();
            $t->string('country')->nullable();
            $t->boolean('is_active')->default(true);
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();
        });
        Schema::create('audit_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('action');
            $t->string('entity_type');
            $t->unsignedBigInteger('entity_id')->nullable();
            $t->string('ip_address', 45)->nullable();
            $t->jsonb('changed_fields')->nullable();
            $t->timestamps();
        });
    }

    private function publishing(Blueprint $t): void
    {
        $t->string('status')->default('draft');
        $t->timestamp('published_at')->nullable();
        $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        $t->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
    }

    public function down(): void
    {
        foreach (['audit_logs', 'university_acceptances', 'staff_profiles', 'contact_channels', 'slideshow_items',
            'menu_item_translations', 'menu_items', 'subject_resource_translations', 'post_translations',
            'page_translations', 'subject_resources', 'posts', 'pages', 'academic_years', 'subject_resource_types',
            'grade_levels', 'post_categories'] as $table) {
            Schema::dropIfExists($table);
        }
        Schema::table('users', function (Blueprint $t) {
            $t->dropConstrainedForeignId('department_id');
            $t->dropConstrainedForeignId('role_id');
            $t->dropColumn(['is_active', 'last_login_at']);
        });
        Schema::dropIfExists('departments');
        Schema::dropIfExists('roles');
    }
};
