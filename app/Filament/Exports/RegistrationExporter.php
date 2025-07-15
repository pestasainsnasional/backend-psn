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
        
            ExportColumn::make('id')->label('ID Pendaftaran'),
            ExportColumn::make('status'),
            ExportColumn::make('competition.name')->label('Kompetisi'),

            ExportColumn::make('competition.competitionType.name')->label('Jenis Kompetisi'),
            ExportColumn::make('registration_batch')
                ->label('Gelombang Pendaftaran')
                ->state(function (Registration $record) {
                    $batch = $record->competition?->competitionType?->current_batch;
                    // Mengubah nama teknis menjadi label yang mudah dibaca
                    if ($batch === 'presale_1') return 'Pre-sale 1';
                    if ($batch === 'presale_2') return 'Pre-sale 2';
                    if ($batch === 'regular') return 'Regular';
                    return 'N/A';
                }),

            ExportColumn::make('user.name')->label('Didaftarkan Oleh'),
            ExportColumn::make('created_at')->label('Tanggal Daftar'),
            
            ExportColumn::make('team.name')->label('Nama Tim'),
            ExportColumn::make('team.school_name')->label('Asal Sekolah'),
            ExportColumn::make('payment_unique_code')->label('Kode Pembayaran'),

            ExportColumn::make('team.companion_teacher_name')->label('Nama Guru'),
            ExportColumn::make('team.companion_teacher_contact')->label('Kontak Guru'),
            ExportColumn::make('team.companion_teacher_email')->label('Email Guru'),

 
            ExportColumn::make('leader_name')
                ->label('Nama Leader')
                ->state(fn (Registration $record) => $record->team?->teamMembers->firstWhere('role', 'leader')?->participant?->full_name ?? 'N/A'),
            ExportColumn::make('leader_email')
                ->label('Email Leader')
                ->state(fn (Registration $record) => $record->team?->teamMembers->firstWhere('role', 'leader')?->participant?->email ?? 'N/A'),
            ExportColumn::make('leader_nisn')
                ->label('NISN Leader')
                ->state(fn (Registration $record) => $record->team?->teamMembers->firstWhere('role', 'leader')?->participant?->nisn ?? 'N/A'),
            ExportColumn::make('leader_phone')
                ->label('No. Telepon Leader')
                ->state(fn (Registration $record) => $record->team?->teamMembers->firstWhere('role', 'leader')?->participant?->phone_number ?? 'N/A'),
            ExportColumn::make('leader_birth_info')
                ->label('TTL Leader')
                ->state(fn (Registration $record) => ($p = $record->team?->teamMembers->firstWhere('role', 'leader')?->participant) ? "{$p->place_of_birth}, {$p->date_of_birth}" : 'N/A'),
            ExportColumn::make('leader_address')
                ->label('Alamat Leader')
                ->state(fn (Registration $record) => $record->team?->teamMembers->firstWhere('role', 'leader')?->participant?->address ?? 'N/A'),


            ExportColumn::make('member1_name')
                ->label('Nama Anggota 1')
                ->state(fn (Registration $record) => $record->team?->teamMembers->where('role', 'member')->values()->get(0)?->participant?->full_name ?? 'N/A'),
            ExportColumn::make('member1_email')
                ->label('Email Anggota 1')
                ->state(fn (Registration $record) => $record->team?->teamMembers->where('role', 'member')->values()->get(0)?->participant?->email ?? 'N/A'),
            ExportColumn::make('member1_nisn')
                ->label('NISN Anggota 1')
                ->state(fn (Registration $record) => $record->team?->teamMembers->where('role', 'member')->values()->get(0)?->participant?->nisn ?? 'N/A'),
            ExportColumn::make('member1_phone')
                ->label('No. Telepon Anggota 1')
                ->state(fn (Registration $record) => $record->team?->teamMembers->where('role', 'member')->values()->get(0)?->participant?->phone_number ?? 'N/A'),
            ExportColumn::make('member1_birth_info')
                ->label('TTL Anggota 1')
                ->state(fn (Registration $record) => ($p = $record->team?->teamMembers->where('role', 'member')->values()->get(0)?->participant) ? "{$p->place_of_birth}, {$p->date_of_birth}" : 'N/A'),
            ExportColumn::make('member1_address')
                ->label('Alamat Anggota 1')
                ->state(fn (Registration $record) => $record->team?->teamMembers->where('role', 'member')->values()->get(0)?->participant?->address ?? 'N/A'),


            ExportColumn::make('member2_name')
                ->label('Nama Anggota 2')
                ->state(fn (Registration $record) => $record->team?->teamMembers->where('role', 'member')->values()->get(1)?->participant?->full_name ?? 'N/A'),
            ExportColumn::make('member2_email')
                ->label('Email Anggota 2')
                ->state(fn (Registration $record) => $record->team?->teamMembers->where('role', 'member')->values()->get(1)?->participant?->email ?? 'N/A'),
            ExportColumn::make('member2_nisn')
                ->label('NISN Anggota 2')
                ->state(fn (Registration $record) => $record->team?->teamMembers->where('role', 'member')->values()->get(1)?->participant?->nisn ?? 'N/A'),
            ExportColumn::make('member2_phone')
                ->label('No. Telepon Anggota 2')
                ->state(fn (Registration $record) => $record->team?->teamMembers->where('role', 'member')->values()->get(1)?->participant?->phone_number ?? 'N/A'),
            ExportColumn::make('member2_birth_info')
                ->label('TTL Anggota 2')
                ->state(fn (Registration $record) => ($p = $record->team?->teamMembers->where('role', 'member')->values()->get(1)?->participant) ? "{$p->place_of_birth}, {$p->date_of_birth}" : 'N/A'),
            ExportColumn::make('member2_address')
                ->label('Alamat Anggota 2')
                ->state(fn (Registration $record) => $record->team?->teamMembers->where('role', 'member')->values()->get(1)?->participant?->address ?? 'N/A'),
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
