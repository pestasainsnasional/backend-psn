<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;

class AdminVerificationNotification extends VerifyEmailBase
{
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'filament.verification.verify',
            Carbon::now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}