<?php

namespace App\Providers;

use App\Models\AcademicProgram;
use App\Models\AcademicYear;
use App\Models\AdmissionFee;
use App\Models\AdmissionForm;
use App\Models\AnnouncementPopup;
use App\Models\AuditLog;
use App\Models\ContactMessage;
use App\Models\CurriculumPillar;
use App\Models\Department;
use App\Models\Director;
use App\Models\Facility;
use App\Models\HeroSlide;
use App\Models\MandarinProgram;
use App\Models\Post;
use App\Models\StaffProfile;
use App\Models\SubjectResource;
use App\Models\UniversityAcceptance;
use App\Models\UniversityLogo;
use App\Models\User;
use App\Policies\AdmissionFeePolicy;
use App\Policies\AdmissionFormPolicy;
use App\Policies\AnnouncementPopupPolicy;
use App\Policies\ContactMessagePolicy;
use App\Policies\GlobalContentPolicy;
use App\Policies\HeroSlidePolicy;
use App\Policies\MandarinProgramPolicy;
use App\Policies\PostPolicy;
use App\Policies\SubjectResourcePolicy;
use App\Policies\UserPolicy;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
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
        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        Gate::policy(Post::class, PostPolicy::class);
        Gate::policy(SubjectResource::class, SubjectResourcePolicy::class);
        Gate::policy(AdmissionForm::class, AdmissionFormPolicy::class);
        Gate::policy(AdmissionFee::class, AdmissionFeePolicy::class);
        Gate::policy(AnnouncementPopup::class, AnnouncementPopupPolicy::class);
        Gate::policy(HeroSlide::class, HeroSlidePolicy::class);
        Gate::policy(MandarinProgram::class, MandarinProgramPolicy::class);
        Gate::policy(ContactMessage::class, ContactMessagePolicy::class);

        foreach ([
            StaffProfile::class,
            UniversityAcceptance::class,
            UniversityLogo::class,
            Department::class,
            AcademicYear::class,
            AcademicProgram::class,
            Director::class,
            Facility::class,
            CurriculumPillar::class,
        ] as $model) {
            Gate::policy($model, GlobalContentPolicy::class);
        }

        Gate::policy(User::class, UserPolicy::class);
        View::composer('components.layouts.site', function ($view) {
            $view->with('announcementPopup', AnnouncementPopup::visible()->orderBy('sort_order')->orderByDesc('id')->first());
        });
        Event::listen(Login::class, function ($event) {
            $event->user->forceFill(['last_login_at' => now()])->saveQuietly();
            AuditLog::create(['user_id' => $event->user->id, 'action' => 'login', 'entity_type' => 'User', 'entity_id' => $event->user->id, 'ip_address' => request()->ip()]);
        });
    }
}
