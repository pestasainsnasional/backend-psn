<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Validation\ValidationException;
use Filament\Http\Responses\Auth\Contracts\LoginResponse; 

class Login extends BaseLogin
{
    protected static string $view = 'filament.pages.auth.login';
    public function authenticate(): LoginResponse
    {
        $data = $this->form->getState();

        if (! \Illuminate\Support\Facades\Auth::guard('web')->attempt($this->getCredentialsFromFormData($data), $data['remember'])) {
            throw ValidationException::withMessages([
                'data.email' => __('filament-panels::pages/auth/login.messages.failed'),
            ]);
        }

        $user = \Illuminate\Support\Facades\Auth::guard('web')->user();

        /** @var \App\Models\User $user */
        if (!$user->hasRole('admin')) {
            \Illuminate\Support\Facades\Auth::guard('web')->logout();
            throw ValidationException::withMessages([
                'data.email' => 'Akses ditolak. Akun ini bukan merupakan admin.',
            ]);
        }
        
        if (!$user->hasVerifiedEmail()) {
            \Illuminate\Support\Facades\Auth::guard('web')->logout();
            throw ValidationException::withMessages([
                'data.email' => 'Email Anda belum terverifikasi. Silakan cek kotak masuk email Anda.',
            ]);
        }

        session()->regenerate();
        return app(LoginResponse::class);
    }
}