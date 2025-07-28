<?php

namespace App\Filament\Exports;

use App\Models\Participant;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Carbon\Carbon; 

class ParticipantExporter extends Exporter
{
    protected static ?string $model = Participant::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID Peserta'),
            ExportColumn::make('full_name')->label('Nama Lengkap'),
            ExportColumn::make('email')->label('Email'),
            ExportColumn::make('nisn')->label('NISN'),
            ExportColumn::make('phone_number')->label('No. Telepon'),
            ExportColumn::make('place_of_birth')->label('Tempat Lahir'),

           
            ExportColumn::make('date_of_birth')
                ->label('Tanggal Lahir')
                ->state(fn (Participant $record) => $record->date_of_birth ? $record->date_of_birth->format('d-m-Y') : null),

            ExportColumn::make('address')->label('Alamat'),

   
            ExportColumn::make('created_at')
                ->label('Tanggal Daftar')
                ->state(fn (Participant $record) => $record->created_at ? Carbon::parse($record->created_at)->format('d-m-Y H:i:s') : null),

       
            ExportColumn::make('updated_at')
                ->label('Terakhir Diperbarui')
                ->state(fn (Participant $record) => $record->updated_at ? Carbon::parse($record->updated_at)->format('d-m-Y H:i:s') : null),

 
            ExportColumn::make('teams_registered')
                ->label('Tim Terdaftar')
                ->state(function (Participant $record) {
                    return $record->teamMembers 
                                  ->map(fn($member) => $member->team?->name)
                                  ->filter()
                                  ->unique()
                                  ->implode(', ');
                }),


            ExportColumn::make('competitions_participated')
                ->label('Kompetisi Diikuti')
                ->state(function (Participant $record) {
                    $competitionName = null;


                    $firstTeamMember = $record->teamMembers->first(); 

                    if ($firstTeamMember) {
                       
                        $team = $firstTeamMember->team; 

                        if ($team) {
                        
                            $firstRegistration = $team->registrations->first(); 

                            if ($firstRegistration) {
                                
                                $competition = $firstRegistration->competition; 

                                if ($competition) {
                                    $competitionName = $competition->name;
                                }
                            }
                        }
                    }
                    return $competitionName ?? 'N/A'; 
                }),


            ExportColumn::make('student_proof_url')
                ->label('URL Bukti Siswa')
                ->state(fn (Participant $record) => $record->getFirstMediaUrl('student-proofs') ?: null),

            ExportColumn::make('twibbon_proof_url')
                ->label('URL Bukti Twibbon')
                ->state(fn (Participant $record) => $record->getFirstMediaUrl('twibbon-proofs') ?: null),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Ekspor data peserta Anda telah selesai dan ' . number_format($export->successful_rows) . ' baris telah diekspor.';
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
        return "Data_Peserta_{$timestamp}";
    }
}