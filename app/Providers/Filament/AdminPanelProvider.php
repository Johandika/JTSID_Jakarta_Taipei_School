<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Dashboard;
use App\Http\Middleware\SetAdminLocale;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path(config('site.admin_path'))
            ->login(Login::class)
            ->brandName(fn (): string => __('admin.brand_name'))
            ->brandLogo(file_exists(public_path('images/logo.webp')) ? asset('images/logo.webp') : asset('images/logo.jpg'))
            ->brandLogoHeight('3.5rem')
            ->darkMode(false)
            ->databaseTransactions()
            ->topbar(false)
            ->globalSearch(false)
            ->maxContentWidth(Width::Full)
            ->font('Plus Jakarta Sans')
            ->renderHook(PanelsRenderHook::STYLES_AFTER, fn () => view('filament.custom-theme'))
            ->renderHook(PanelsRenderHook::SIDEBAR_FOOTER, fn () => view('filament.sidebar-user-menu'))
            ->navigationGroups([
                'Content' => NavigationGroup::make(fn (): string => __('admin.nav_groups.content')),
                'Website' => NavigationGroup::make(fn (): string => __('admin.nav_groups.website')),
                'School' => NavigationGroup::make(fn (): string => __('admin.nav_groups.school')),
                'Admissions' => NavigationGroup::make(fn (): string => __('admin.nav_groups.admissions')),
                'Settings' => NavigationGroup::make(fn (): string => __('admin.nav_groups.settings')),
            ])
            ->userMenu(false)
            ->colors([
                'primary' => Color::hex('#176443'),
                'gray' => Color::Zinc,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                SetAdminLocale::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
