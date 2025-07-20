<?php

namespace App\Filament\Resources\RegistrationResource\Pages;

use App\Filament\Resources\RegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use App\Models\TeamMember;
use App\Models\Registration;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Actions\Action;

class ViewRegistration extends ViewRecord
{
    protected static string $resource = RegistrationResource::class;

    protected function getRecordQuery(): Builder
    {
        return parent::getRecordQuery()->with([
            'competition',
            'user',
            'team.media',
            'team.teamMembers.participant.media'
        ]);
    }

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
                Components\Section::make('Detail Pendaftaran')
                    ->schema([
                        Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'pending' => 'warning',
                                'verified' => 'success',
                                'rejected' => 'danger',
                                default => 'gray',
                            }),
                        Components\TextEntry::make('competition.name')->label('Kompetisi'),
                        Components\TextEntry::make('user.name')->label('Didaftarkan Oleh'),
                        Components\TextEntry::make('created_at')->label('Tanggal Daftar')->dateTime(),
                    ])
                    ->columns(2),

                Components\Section::make('Detail Tim & Pembayaran')
                    ->schema([
                        Components\TextEntry::make('team.name')->label('Nama Tim'),
                        Components\TextEntry::make('team.school_name')->label('Nama Sekolah'),
                        Components\TextEntry::make('payment_unique_code')->label('Kode Pembayaran')->copyable(),

                        // Bukti Pembayaran dengan Actions
                        Components\Actions::make([
                            Action::make('viewPaymentProof')
                                ->label('Lihat Bukti Pembayaran')
                                ->icon('heroicon-o-eye')
                                ->color('info')
                                ->modalContent(
                                    fn(Registration $record): \Illuminate\Contracts\View\View =>
                                    view('filament.modals.view-media', [
                                        'media' => $record->team?->getFirstMedia('payment-proofs')
                                    ])
                                )
                                ->modalSubmitAction(false)
                                ->modalCancelActionLabel('Tutup')
                                ->visible(fn(Registration $record) => $record->team?->hasMedia('payment-proofs')),

                            Action::make('downloadPaymentProof')
                                ->label('Unduh Bukti Pembayaran')
                                ->icon('heroicon-o-arrow-down-tray')
                                ->color('success')
                                ->action(function (Registration $record) {
                                    $mediaItem = $record->team?->getFirstMedia('payment-proofs');
                                    if ($mediaItem) {
                                        return response()->download($mediaItem->getPath(), $mediaItem->file_name);
                                    }

                                    $this->notify('danger', 'File tidak ditemukan');
                                    return null;
                                })
                                ->visible(fn(Registration $record) => $record->team?->hasMedia('payment-proofs')),
                        ])
                            ->visible(fn(Registration $record) => $record->team?->hasMedia('payment-proofs')),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->hidden(fn(Registration $record) => is_null($record->team)),

                Components\Section::make('Guru Pendamping')
                    ->schema([
                        Components\TextEntry::make('team.companion_teacher_name')->label('Nama'),
                        Components\TextEntry::make('team.companion_teacher_contact')->label('Kontak'),
                        Components\TextEntry::make('team.companion_teacher_email')->label('Email'),
                    ])
                    ->collapsible()
                    ->hidden(fn(Registration $record) => is_null($record->team?->companion_teacher_name)),

                Components\Section::make('Peserta Tim')
                    ->schema([
                        Components\RepeatableEntry::make('team.teamMembers')
                            ->label('')
                            ->schema([
                                Components\Group::make()
                                    ->schema([
                                        Components\TextEntry::make('participant.full_name')
                                            ->label('Nama Peserta')
                                            ->weight('bold'),

                                        Components\TextEntry::make('role')
                                            ->badge(),

                                        Components\TextEntry::make('participant.email')
                                            ->label('Email'),

                                        Components\TextEntry::make('participant.phone_number')
                                            ->label('No. Telepon')
                                            ->visible(fn(TeamMember $record) => !empty($record->participant?->phone_number)),

                                        Components\TextEntry::make('participant.place_of_birth')
                                            ->label('Tempat Lahir')
                                            ->visible(fn(TeamMember $record) => !empty($record->participant?->place_of_birth)),

                                        Components\TextEntry::make('participant.date_of_birth')
                                            ->label('Tanggal Lahir')
                                            ->date()
                                            ->visible(fn(TeamMember $record) => !empty($record->participant?->date_of_birth)),

                                        Components\TextEntry::make('participant.address')
                                            ->label('Alamat')
                                            ->visible(fn(TeamMember $record) => !empty($record->participant?->address)),

                                        Components\TextEntry::make('participant.nisn')
                                            ->label('NISN')
                                            ->visible(fn(TeamMember $record) => !empty($record->participant?->nisn)),

                   
                                        Components\Section::make('Dokumen Peserta')
                                            ->schema([
                                                Components\Grid::make(2) 
                                                    ->schema([
                
                                                        Components\Actions::make([
                                                            Action::make('viewStudentProof')
                                                                ->label('Lihat Bukti Siswa')
                                                                ->icon('heroicon-o-eye')
                                                                ->color('info')
                                                                ->modalContent(
                                                                    fn(TeamMember $record): \Illuminate\Contracts\View\View =>
                                                                    view('filament.modals.view-media', [
                                                                        'media' => $record->participant?->getFirstMedia('student-proofs')
                                                                    ])
                                                                )
                                                                ->modalSubmitAction(false)
                                                                ->modalCancelActionLabel('Tutup')
                                                                ->visible(fn(TeamMember $record) => $record->participant?->hasMedia('student-proofs')),

                                                            Action::make('downloadStudentProof')
                                                                ->label('Unduh Bukti Siswa')
                                                                ->icon('heroicon-o-arrow-down-tray')
                                                                ->color('success')
                                                                ->action(function (TeamMember $record) {
                                                                    $mediaItem = $record->participant?->getFirstMedia('student-proofs');
                                                                    if ($mediaItem) {
                                                                        return response()->download($mediaItem->getPath(), $mediaItem->file_name);
                                                                    }

                                                                    $this->notify('danger', 'File tidak ditemukan');
                                                                    return null;
                                                                })
                                                                ->visible(fn(TeamMember $record) => $record->participant?->hasMedia('student-proofs')),
                                                        ])
                                                            ->label('Kartu Pelajar')
                                                            ->visible(fn(TeamMember $record) => $record->participant?->hasMedia('student-proofs')),

                                                        Components\Actions::make([
                                                            Action::make('viewTwibbonProof')
                                                                ->label('Lihat Bukti Twibbon')
                                                                ->icon('heroicon-o-eye')
                                                                ->color('info')
                                                                ->modalContent(
                                                                    fn(TeamMember $record): \Illuminate\Contracts\View\View =>
                                                                    view('filament.modals.view-media', [
                                                                        'media' => $record->participant?->getFirstMedia('twibbon-proofs')
                                                                    ])
                                                                )
                                                                ->modalSubmitAction(false)
                                                                ->modalCancelActionLabel('Tutup')
                                                                ->visible(fn(TeamMember $record) => $record->participant?->hasMedia('twibbon-proofs')),

                                                            Action::make('downloadTwibbonProof')
                                                                ->label('Unduh Bukti Twibbon')
                                                                ->icon('heroicon-o-arrow-down-tray')
                                                                ->color('success')
                                                                ->action(function (TeamMember $record) {
                                                                    $mediaItem = $record->participant?->getFirstMedia('twibbon-proofs');
                                                                    if ($mediaItem) {
                                                                        return response()->download($mediaItem->getPath(), $mediaItem->file_name);
                                                                    }

                                                                    $this->notify('danger', 'File tidak ditemukan');
                                                                    return null;
                                                                })
                                                                ->visible(fn(TeamMember $record) => $record->participant?->hasMedia('twibbon-proofs')),
                                                        ])
                                                            ->label('Bukti Twibbon')
                                                            ->visible(fn(TeamMember $record) => $record->participant?->hasMedia('twibbon-proofs')),

                                                    ]),
                                            ])
                                            ->compact()
                                            ->visible(
                                                fn(TeamMember $record) =>
                                                $record->participant?->hasMedia('student-proofs') ||
                                                    $record->participant?->hasMedia('twibbon-proofs') ||
                                                    $record->participant?->hasMedia('photos')
                                            ),
                                    ])
                                    ->hidden(fn(TeamMember $record): bool => is_null($record->participant)),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->hidden(fn(Registration $record) => is_null($record->team)),
            ]);
    }
}
