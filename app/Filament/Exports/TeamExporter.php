<?php

namespace App\Filament\Exports;

use App\Models\Team;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Carbon\Carbon;

class TeamExporter extends Exporter
{
    protected static ?string $model = Team::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID Tim'),
            ExportColumn::make('name')->label('Nama Tim'),
            ExportColumn::make('school_name')->label('Nama Sekolah'),
            ExportColumn::make('school_email')->label('Email Sekolah'),
            ExportColumn::make('npsn')->label('NPSN'),

            ExportColumn::make('payment_proof_url')
                ->label('URL Bukti Pembayaran Tim')
                ->state(fn (Team $record) => $record->getFirstMediaUrl('payment-proofs') ?: null),

            ExportColumn::make('companion_teacher_name')->label('Nama Guru Pendamping'),
            ExportColumn::make('companion_teacher_contact')->label('Kontak Guru Pendamping'),
            ExportColumn::make('companion_teacher_email')->label('Email Guru Pendamping'),
            ExportColumn::make('companion_teacher_nip')->label('NIP Guru Pendamping'),
            ExportColumn::make('created_at')->label('Tanggal Dibuat')->dateTime(),
            ExportColumn::make('updated_at')->label('Terakhir Diperbarui')->dateTime(),
            ExportColumn::make('deleted_at')->label('Tanggal Dihapus')->dateTime(),

            ExportColumn::make('competitions_registered')
                ->label('Kompetisi Diikuti')
                ->state(function (Team $record) {
                    return $record->registrations
                                  ->map(fn($registration) => $registration->competition?->name)
                                  ->filter()
                                  ->unique()
                                  ->implode(', ');
                }),

            ExportColumn::make('leader_name')
                ->label('Nama Leader')
                ->state(fn (Team $record) => $record->teamMembers->firstWhere('role', 'leader')?->participant?->full_name ?? 'N/A'),
            ExportColumn::make('leader_email')
                ->label('Email Leader')
                ->state(fn (Team $record) => $record->teamMembers->firstWhere('role', 'leader')?->participant?->email ?? 'N/A'),
            ExportColumn::make('leader_nisn')
                ->label('NISN Leader')
                ->state(fn (Team $record) => $record->teamMembers->firstWhere('role', 'leader')?->participant?->nisn ?? 'N/A'),
            ExportColumn::make('leader_phone')
                ->label('No. Telepon Leader')
                ->state(fn (Team $record) => $record->teamMembers->firstWhere('role', 'leader')?->participant?->phone_number ?? 'N/A'),
            ExportColumn::make('leader_birth_info')
                ->label('TTL Leader')
                ->state(fn (Team $record) => ($p = $record->teamMembers->firstWhere('role', 'leader')?->participant) ? "{$p->place_of_birth}, {$p->date_of_birth->format('d-m-Y')}" : 'N/A'),
            ExportColumn::make('leader_address')
                ->label('Alamat Leader')
                ->state(fn (Team $record) => $record->teamMembers->firstWhere('role', 'leader')?->participant?->address ?? 'N/A'),
            ExportColumn::make('leader_student_proof_url')
                ->label('URL Bukti Siswa Leader')
                ->state(fn (Team $record) => $record->teamMembers->firstWhere('role', 'leader')?->participant?->getFirstMediaUrl('student-proofs') ?? null),
            ExportColumn::make('leader_twibbon_proof_url')
                ->label('URL Bukti Twibbon Leader')
                ->state(fn (Team $record) => $record->teamMembers->firstWhere('role', 'leader')?->participant?->getFirstMediaUrl('twibbon-proofs') ?? null),

            ExportColumn::make('member1_name')
                ->label('Nama Anggota 1')
                ->state(fn (Team $record) => $record->teamMembers->where('role', 'member')->values()->get(0)?->participant?->full_name ?? 'N/A'),
            ExportColumn::make('member1_email')
                ->label('Email Anggota 1')
                ->state(fn (Team $record) => $record->teamMembers->where('role', 'member')->values()->get(0)?->participant?->email ?? 'N/A'),
            ExportColumn::make('member1_nisn')
                ->label('NISN Anggota 1')
                ->state(fn (Team $record) => $record->teamMembers->where('role', 'member')->values()->get(0)?->participant?->nisn ?? 'N/A'),
            ExportColumn::make('member1_phone')
                ->label('No. Telepon Anggota 1')
                ->state(fn (Team $record) => $record->teamMembers->where('role', 'member')->values()->get(0)?->participant?->phone_number ?? 'N/A'),
            ExportColumn::make('member1_birth_info')
                ->label('TTL Anggota 1')
                ->state(fn (Team $record) => ($p = $record->teamMembers->where('role', 'member')->values()->get(0)?->participant) ? "{$p->place_of_birth}, {$p->date_of_birth->format('d-m-Y')}" : 'N/A'),
            ExportColumn::make('member1_address')
                ->label('Alamat Anggota 1')
                ->state(fn (Team $record) => $record->teamMembers->where('role', 'member')->values()->get(0)?->participant?->address ?? 'N/A'),
            ExportColumn::make('member1_student_proof_url')
                ->label('URL Bukti Siswa Anggota 1')
                ->state(fn (Team $record) => $record->teamMembers->where('role', 'member')->values()->get(0)?->participant?->getFirstMediaUrl('student-proofs') ?? null),
            ExportColumn::make('member1_twibbon_proof_url')
                ->label('URL Bukti Twibbon Anggota 1')
                ->state(fn (Team $record) => $record->teamMembers->where('role', 'member')->values()->get(0)?->participant?->getFirstMediaUrl('twibbon-proofs') ?? null),

            ExportColumn::make('member2_name')
                ->label('Nama Anggota 2')
                ->state(fn (Team $record) => $record->teamMembers->where('role', 'member')->values()->get(1)?->participant?->full_name ?? 'N/A'),
            ExportColumn::make('member2_email')
                ->label('Email Anggota 2')
                ->state(fn (Team $record) => $record->teamMembers->where('role', 'member')->values()->get(1)?->participant?->email ?? 'N/A'),
            ExportColumn::make('member2_nisn')
                ->label('NISN Anggota 2')
                ->state(fn (Team $record) => $record->teamMembers->where('role', 'member')->values()->get(1)?->participant?->nisn ?? 'N/A'),
            ExportColumn::make('member2_phone')
                ->label('No. Telepon Anggota 2')
                ->state(fn (Team $record) => $record->teamMembers->where('role', 'member')->values()->get(1)?->participant?->phone_number ?? 'N/A'),
            ExportColumn::make('member2_birth_info')
                ->label('TTL Anggota 2')
                ->state(fn (Team $record) => ($p = $record->teamMembers->where('role', 'member')->values()->get(1)?->participant) ? "{$p->place_of_birth}, {$p->date_of_birth->format('d-m-Y')}" : 'N/A'),
            ExportColumn::make('member2_address')
                ->label('Alamat Anggota 2')
                ->state(fn (Team $record) => $record->teamMembers->where('role', 'member')->values()->get(1)?->participant?->address ?? 'N/A'),
            ExportColumn::make('member2_student_proof_url')
                ->label('URL Bukti Siswa Anggota 2')
                ->state(fn (Team $record) => $record->teamMembers->where('role', 'member')->values()->get(1)?->participant?->getFirstMediaUrl('student-proofs') ?? null),
            ExportColumn::make('member2_twibbon_proof_url')
                ->label('URL Bukti Twibbon Anggota 2')
                ->state(fn (Team $record) => $record->teamMembers->where('role', 'member')->values()->get(1)?->participant?->getFirstMediaUrl('twibbon-proofs') ?? null),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Ekspor data tim Anda telah selesai dan ' . number_format($export->successful_rows) . ' baris telah diekspor.';
        if ($failedRowsCount = $export->failed_rows) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal diekspor.';
        }
        return $body;
    }

    public function getJobConnection(): ?string
    {
        return 'sync';
    }

    public function getFileName(Export $export): string
    {
        $timestamp = Carbon::now()->format('d-m-Y_H-i-s');
        return "Data_Tim_{$timestamp}";
    }
}