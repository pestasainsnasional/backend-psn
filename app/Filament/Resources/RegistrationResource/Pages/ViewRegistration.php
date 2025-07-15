<?php
// File: app/Filament/Resources/RegistrationResource/Pages/ViewRegistration.php

namespace App\Filament\Resources\RegistrationResource\Pages;

use App\Filament\Resources\RegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use App\Models\TeamMember;
use App\Models\Registration;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Illuminate\Support\Collection;

class ViewRegistration extends ViewRecord
{
    protected static string $resource = RegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Kita tidak lagi menggunakan Grid dan Group untuk layout utama.
                // Semua seksi akan ditampilkan secara berurutan dari atas ke bawah.

                Components\Section::make('Detail Pendaftaran')
                    ->schema([
<<<<<<< Updated upstream
                        // KOLOM KIRI (INFO UTAMA)
                        Components\Group::make()
                            ->schema([
                                Components\Section::make('Detail Pendaftaran')
                                    ->schema([
                                        Components\TextEntry::make('status')->badge()->color(fn(string $state): string => match ($state) {
                                            'pending' => 'warning',
                                            'verified' => 'success',
                                            'rejected' => 'danger',
                                            default => 'gray',
                                        }),
                                        Components\TextEntry::make('competition.name')->label('Kompetisi'),
                                        Components\TextEntry::make('user.name')->label('Didaftarkan Oleh'),
                                        Components\TextEntry::make('created_at')->label('Tanggal Daftar')->dateTime(),
                                    ])->columns(2),

                                Components\Section::make('Detail Tim & Pembayaran')
                                    ->schema([
                                        Components\TextEntry::make('team.name')->label('Nama Tim'),
                                        Components\TextEntry::make('team.school_name')->label('Nama Sekolah'),
                                        Components\TextEntry::make('payment_unique_code')->label('Kode Pembayaran')->copyable(),

                                    ])
                                    ->columns(2)
                                    ->collapsible()
                                    ->hidden(fn(Registration $record) => is_null($record->team)),

                                
                            ])->columnSpan(2),

                        // KOLOM KANAN (PESERTA & GURU)
                        Components\Group::make()
                            ->schema([
                                Components\Section::make('Guru Pendamping')
                                    ->schema([
                                        Components\TextEntry::make('team.companion_teacher_name')->label('Nama'),
                                        Components\TextEntry::make('team.companion_teacher_contact')->label('Kontak'),
                                        Components\TextEntry::make('team.companion_teacher_email')->label('Email'),
                                    ])
                                    ->collapsible() // <-- BISA DI-DROPDOWN
                                    ->hidden(fn(Registration $record) => is_null($record->team?->companion_teacher_name)),

                                Components\Section::make('Peserta Tim')
                                    ->schema([
                                        Components\RepeatableEntry::make('team.teamMembers')
                                            ->label('')
                                            ->schema([

                                                Components\Group::make()->hidden(fn($record): bool => is_null($record->participant))->schema([
                                                    Components\TextEntry::make('participant.full_name')->label('Nama Peserta')->weight('bold'),
                                                    Components\TextEntry::make('role')->badge(),
                                                    Components\TextEntry::make('participant.email')->label('Email'),
                                                    Components\TextEntry::make('participant.nisn')->label('NISN'),
                                                    Components\TextEntry::make('participant.phone_number')->label('No. Telepon'),
                                                    Components\TextEntry::make('participant.place_of_birth')->label('Tempat Lahir'),
                                                    Components\TextEntry::make('participant.date_of_birth')->label('Tanggal Lahir')->date(),
                                                    Components\TextEntry::make('participant.address')->label('Alamat')->columnSpanFull(),


                                                ])->hidden(fn(TeamMember $record): bool => is_null($record->participant)),
                                            ])->columns(1),
                                    ])
                                    ->collapsible()
                                    ->collapsed(),
                            ])->columnSpan(1)
                            ->hidden(fn(Registration $record) => is_null($record->team)),
                    ]),
=======
                        Components\TextEntry::make('status')->badge()->color(fn (string $state): string => match ($state) {
                            'pending' => 'warning', 'verified' => 'success', 'rejected' => 'danger', default => 'gray',
                        }),
                        Components\TextEntry::make('competition.name')->label('Kompetisi'),
                        Components\TextEntry::make('user.name')->label('Didaftarkan Oleh'),
                        Components\TextEntry::make('created_at')->label('Tanggal Daftar')->dateTime(),
                    ])->columns(2),
                
                Components\Section::make('Detail Tim & Pembayaran')
                    ->schema([
                        Components\TextEntry::make('team.name')->label('Nama Tim'),
                        Components\TextEntry::make('team.school_name')->label('Nama Sekolah'),
                        Components\TextEntry::make('payment_unique_code')->label('Kode Pembayaran')->copyable(),
                        // Media untuk bukti pembayaran dihapus sementara
                    ])
                    ->columns(2)
                    ->collapsible() 
                    ->hidden(fn (Registration $record) => is_null($record->team)),

                Components\Section::make('Guru Pendamping')
                    ->schema([
                        Components\TextEntry::make('team.companion_teacher_name')->label('Nama'),
                        Components\TextEntry::make('team.companion_teacher_contact')->label('Kontak'),
                        Components\TextEntry::make('team.companion_teacher_email')->label('Email'),
                    ])
                    ->collapsible()
                    ->hidden(fn (Registration $record) => is_null($record->team?->companion_teacher_name)),
                
                Components\Section::make('Peserta Tim')
                    ->schema([
                        Components\RepeatableEntry::make('team.teamMembers')
                            ->label('')
                            ->schema([
                                Components\Group::make()->schema([
                                    Components\TextEntry::make('participant.full_name')->label('Nama Peserta')->weight('bold'),
                                    Components\TextEntry::make('role')->badge(),
                                    Components\TextEntry::make('participant.email')->label('Email'),
                                    Components\TextEntry::make('participant.nisn')->label('NISN'),
                                    Components\TextEntry::make('participant.phone_number')->label('No. Telepon'),
                                    Components\TextEntry::make('participant.place_of_birth')->label('Tempat Lahir'),
                                    Components\TextEntry::make('participant.date_of_birth')->label('Tanggal Lahir')->date(),
                                    Components\TextEntry::make('participant.address')->label('Alamat')->columnSpanFull(),
                                ])->hidden(fn (TeamMember $record): bool => is_null($record->participant)),
                            ])->columns(1),
                    ])
                    ->collapsible() 
                    ->collapsed(), 
>>>>>>> Stashed changes
            ]);
    }
}
