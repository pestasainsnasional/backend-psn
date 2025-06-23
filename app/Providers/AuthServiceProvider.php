<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\User;


class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [

    ];

    public function boot(): void
    {
        VerifyEmail::createUrlUsing(function (object $notifiable) {
            return URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(60),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );
        });

        Gate::define('access-admin-dashboard', function (User $user) {
            return $user->hasRole('admin')
                        ? Response::allow()
                        : Response::deny('Anda tidak memiliki hak akses sebagai admin.');
        });
    }
}