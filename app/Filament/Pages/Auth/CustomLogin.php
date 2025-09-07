<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Auth\Pages\Login;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Illuminate\Validation\ValidationException;

class CustomLogin extends Login
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getAdminLoginFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getRememberFormComponent(),
            ]);
    }
    

 protected function getAdminLoginFormComponent(): Component
    {
        return TextInput::make('login')
            ->label(__('Username / Email'))
            
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }
//  protected function throwFailureValidationException(): never
//     {
//         throw ValidationException::withMessages([
//             'data.login' => __('filament-panels::pages/auth/login.messages.failed'),
//         ]);
//     }
    // protected function getPasswordFormComponent(): Component
    // {
    //     return TextInput::make('password')
    //         ->label(__('filament-panels::pages/auth/login.form.password.label'))
    //         ->hint(filament()->hasPasswordReset() ? new HtmlString(Blade::render('<x-filament::link :href="filament()->getRequestPasswordResetUrl()" tabindex="3"> {{ __(\'filament-panels::pages/auth/login.actions.request_password_reset.label\') }}</x-filament::link>')) : null)
    //         ->password()
    //         ->revealable(filament()->arePasswordsRevealable())
    //         ->autocomplete('current-password')
    //         ->required()
    //         ->extraInputAttributes(['tabindex' => 2]);
    // }

    protected function getRememberFormComponent(): Component
    {
        return Checkbox::make('remember')
        ->label(__('filament-panels::auth/pages/login.form.remember.label'));
    }

     protected function getCredentialsFromFormData(array $data): array
    {

        $login_type=filter_var($data['login'],FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        return [
            $login_type=> $data['login'],
            'password' => $data['password'],
        ];
    }
}
