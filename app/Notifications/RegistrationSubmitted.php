<?php
// File: app/Notifications/RegistrationSubmitted.php

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
        $this->registration = $registration->load(['team', 'competition.competitionType', 'participant']);
    }


    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {

        $competitionType = $this->registration->competition->competitionType->type;
        $competitionName = $this->registration->competition->name;
        $profileUrl = env('FRONTEND_URL', url('/')) . '/profile'; 

        $mailMessage = (new MailMessage)
                    ->subject('Pendaftaran Anda Telah Diterima: ' . $competitionName)
                    ->greeting('Halo, ' . $notifiable->name . '!')
                    ->line('Terima kasih telah menyelesaikan pendaftaran Anda.');


        if ($competitionType === 'individu') {
            $leaderName = $this->registration->participant->full_name;
            $mailMessage->line('Pendaftaran Anda untuk kompetisi "' . $competitionName . '" atas nama **' . $leaderName . '** telah kami terima.');
        } else {
            $teamName = $this->registration->team->name;
            $mailMessage->line('Pendaftaran tim Anda, **"' . $teamName . '"**, untuk kompetisi "' . $competitionName . '" telah kami terima.');
        }
  

        return $mailMessage
                    ->line('Data Anda akan segera masuk ke dalam antrean untuk diverifikasi oleh tim kami. Anda akan menerima email pemberitahuan selanjutnya setelah proses verifikasi selesai.')
                    ->action('Lihat Status Pendaftaran', $profileUrl)
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
