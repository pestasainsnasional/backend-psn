<?php
// File: app/Notifications/RegistrationVerified.php

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
        $groupLink = 'https://ipb.link/group-peserta-psn2025';

        $mailMessage = (new MailMessage)
                    ->subject('Pendaftaran Terverifikasi: ' . $competitionName)
                    ->greeting('Selamat, ' . $notifiable->name . '!')
                    ->line('Kami dengan senang hati menginformasikan bahwa pendaftaran Anda telah berhasil kami verifikasi.');

 
        if ($competitionType === 'individu') {
            $leaderName = $this->registration->participant->full_name;
            $mailMessage->line('Pendaftaran Anda untuk kompetisi "' . $competitionName . '" atas nama **' . $leaderName . '** telah dikonfirmasi.');
        } else {
            $teamName = $this->registration->team->name;
            $mailMessage->line('Pendaftaran tim Anda, **"' . $teamName . '"**, untuk kompetisi "' . $competitionName . '" telah dikonfirmasi.');
        }

        return $mailMessage
                    ->line('Anda sekarang resmi terdaftar sebagai peserta. Langkah selanjutnya adalah bergabung dengan grup WhatsApp peserta melalui tautan di bawah ini untuk mendapatkan informasi penting terkait teknis lomba.')
                    ->line('Grup Peserta: **' . $groupLink . '**') 
                    ->action('Kunjungi halaman profile anda', $profileUrl)
                    ->line('Terima kasih atas partisipasi Anda. Sampai jumpa di Pesta Sains Nasional 2025!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
