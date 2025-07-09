<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Registration;

class RegistrationVerified extends Notification
{
    use Queueable;
    protected $registration;
    

    public function __construct(Registration $registration)
    {
        $this->registration = $registration;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }



    public function toMail(object $notifiable): MailMessage
    {
        $teamName = $this->registration->team->name;
        $url = url('/dashboard'); 

        return (new MailMessage)
                    ->subject('Selamat! Pendaftaran Anda Telah Diverifikasi - ' . $teamName)
                    ->greeting('Kabar Baik, ' . $notifiable->name . '!')
                    ->line('Kami dengan senang hati memberitahukan bahwa pendaftaran Anda untuk tim "' . $teamName . '" telah berhasil diverifikasi oleh admin.')
                    ->line('Anda sekarang resmi menjadi peserta. Silakan persiapkan diri Anda untuk kompetisi.')
                    ->action('Masuk ke Dashboard', $url)
                    ->line('Sampai jumpa di hari acara!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
