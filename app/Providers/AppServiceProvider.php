<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;
use App\Models\Export; 
use App\Models\Import;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
      
        $this->app->bind(
            \Filament\Actions\Exports\Models\Export::class,
            Export::class
        );

   
        $this->app->bind(
            \Filament\Actions\Imports\Models\Import::class,
            Import::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        Sanctum::usePersonalAccessTokenModel(\App\Models\Sanctum\PersonalAccessToken::class);
    }

    
}
