<?php

namespace App\Filament\Pages\Auth;

use Filament\Actions\Action;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseLogin
{
    protected string $view = 'filament.pages.auth.login';

    protected static string $layout = 'filament.pages.auth.layout';

    public function getTitle(): string|Htmlable
    {
        return __('admin.login.title').' - '.config('app.name', 'Jakarta Taipei School');
    }

    public function getHeading(): string|Htmlable|null
    {
        return __('admin.login.title');
    }

    public function getSubheading(): string|Htmlable|null
    {
        return __('admin.login.subtitle');
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label(__('admin.login.email'))
            ->placeholder(__('admin.login.email_placeholder'))
            ->email()
            ->required()
            ->autocomplete('email')
            ->autofocus();
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label(__('admin.login.password'))
            ->placeholder(__('admin.login.password_placeholder'))
            ->password()
            ->revealable(true)
            ->autocomplete('current-password')
            ->required();
    }

    protected function getRememberFormComponent(): Component
    {
        return Checkbox::make('remember')
            ->label(__('admin.login.remember_me'));
    }

    protected function getAuthenticateFormAction(): Action
    {
        return Action::make('authenticate')
            ->label(__('admin.login.submit'))
            ->submit('authenticate');
    }
}
