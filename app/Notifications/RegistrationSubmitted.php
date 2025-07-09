<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Registration;

class RegistrationSubmitted extends Notification
{
    use Queueable;
    protected $registration;

    /**
     * Create a new notification instance.
     */
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
        $url = url('/dashboard/status'); 


        return (new MailMessage)
            ->subject('Pendaftaran anda telah diterima -' . $teamName)
             ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Terima kasih telah menyelesaikan pendaftaran untuk tim "' . $teamName . '".')
            ->line('Data Anda telah kami terima dan akan segera masuk ke dalam antrean untuk diverifikasi oleh tim kami.')
            ->action('Lihat Status Pendaftaran', $url)
            ->line('Terima kasih telah menjadi bagian dari acara kami!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
