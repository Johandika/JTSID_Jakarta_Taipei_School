<?php

namespace App\Providers;

use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\ContactChannel;
use App\Models\Department;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Post;
use App\Models\SlideshowItem;
use App\Models\StaffProfile;
use App\Models\SubjectResource;
use App\Models\UniversityAcceptance;
use App\Models\User;
use App\Policies\ContentPolicy;
use App\Policies\GlobalContentPolicy;
use App\Policies\UserPolicy;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        foreach ([Post::class, SubjectResource::class, StaffProfile::class, UniversityAcceptance::class] as $model) {
            Gate::policy($model, ContentPolicy::class);
        }
        foreach ([Page::class, MenuItem::class, SlideshowItem::class, ContactChannel::class, Department::class, AcademicYear::class] as $model) {
            Gate::policy($model, GlobalContentPolicy::class);
        }
        Gate::policy(User::class, UserPolicy::class);
        View::composer('components.layouts.site', function ($view) {
            $view->with('navigation', MenuItem::where('is_active', true)->whereNull('parent_id')->with(['translations', 'children.translations'])->orderBy('sort_order')->get());
            $view->with('contacts', ContactChannel::orderBy('sort_order')->get());
        });
        Event::listen(Login::class, function ($event) {
            $event->user->forceFill(['last_login_at' => now()])->saveQuietly();
            AuditLog::create(['user_id' => $event->user->id, 'action' => 'login', 'entity_type' => 'User', 'entity_id' => $event->user->id, 'ip_address' => request()->ip()]);
        });
    }
}
