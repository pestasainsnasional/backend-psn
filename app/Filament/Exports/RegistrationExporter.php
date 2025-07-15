<?php

namespace App\Filament\Exports;

use App\Models\Registration;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class RegistrationExporter extends Exporter
{
    protected static ?string $model = Registration::class;

    public static function getColumns(): array
    {
        return [
            // Kolom dari relasi langsung
            ExportColumn::make('id')->label('ID Pendaftaran'),
            ExportColumn::make('competition.name')->label('Kompetisi'),
            ExportColumn::make('team.name')->label('Nama Tim'),
            ExportColumn::make('team.school_name')->label('Asal Sekolah'),
            ExportColumn::make('status'),
            ExportColumn::make('team.companion_teacher_name')->label('Nama Guru Pendamping'),
            ExportColumn::make('team.companion_teacher_contact')->label('Kontak Guru'),

            // --- Kolom Kustom untuk Peserta (menggunakan state()) ---

            // Kolom untuk Leader
            ExportColumn::make('leader_name')
                ->label('Nama Leader')
                ->state(function (Registration $record) {
                    // Cari anggota dengan peran 'leader' dan ambil nama participant-nya
                    return $record->team?->teamMembers->firstWhere('role', 'leader')?->participant?->full_name ?? 'N/A';
                }),
            ExportColumn::make('leader_nisn')
                ->label('NISN Leader')
                ->state(function (Registration $record) {
                    return $record->team?->teamMembers->firstWhere('role', 'leader')?->participant?->nisn ?? 'N/A';
                }),
            ExportColumn::make('leader_email')
                ->label('Email Leader')
                ->state(function (Registration $record) {
                    return $record->team?->teamMembers->firstWhere('role', 'leader')?->participant?->email ?? 'N/A';
                }),

            // Kolom untuk Anggota 1
            ExportColumn::make('member1_name')
                ->label('Nama Anggota 1')
                ->state(function (Registration $record) {
                    // Ambil semua anggota (bukan leader), lalu ambil yang pertama (index 0)
                    return $record->team?->teamMembers->where('role', 'member')->values()->get(0)?->participant?->full_name ?? 'N/A';
                }),
            ExportColumn::make('member1_nisn')
                ->label('NISN Anggota 1')
                ->state(function (Registration $record) {
                    return $record->team?->teamMembers->where('role', 'member')->values()->get(0)?->participant?->nisn ?? 'N/A';
                }),
            ExportColumn::make('member1_email')
                ->label('Email Anggota 1')
                ->state(function (Registration $record) {
                    return $record->team?->teamMembers->where('role', 'member')->values()->get(0)?->participant?->email ?? 'N/A';
                }),

            // Kolom untuk Anggota 2
            ExportColumn::make('member2_name')
                ->label('Nama Anggota 2')
                ->state(function (Registration $record) {
                    // Ambil semua anggota, lalu ambil yang kedua (index 1)
                    return $record->team?->teamMembers->where('role', 'member')->values()->get(1)?->participant?->full_name ?? 'N/A';
                }),
            ExportColumn::make('member2_nisn')
                ->label('NISN Anggota 2')
                ->state(function (Registration $record) {
                    return $record->team?->teamMembers->where('role', 'member')->values()->get(1)?->participant?->nisn ?? 'N/A';
                }),
             ExportColumn::make('member2_email')
                ->label('Email Anggota 2')
                ->state(function (Registration $record) {
                    return $record->team?->teamMembers->where('role', 'member')->values()->get(1)?->participant?->email ?? 'N/A';
                }),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Ekspor data pendaftaran Anda telah selesai dan ' . number_format($export->successful_rows) . ' baris telah diekspor.';

        if ($failedRowsCount = $export->failed_rows) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal diekspor.';
        }

        return $body;
    }

  
    public function getJobConnection(): ?string
    {
        return 'sync';
    }
}
