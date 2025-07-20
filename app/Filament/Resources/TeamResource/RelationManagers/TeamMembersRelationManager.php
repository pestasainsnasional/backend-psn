<?php

namespace App\Filament\Resources\TeamResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder; // <-- 1. TAMBAHKAN IMPORT INI
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Tables\Actions\Action;
use App\Models\TeamMember; // <-- Import untuk type-hinting

class TeamMembersRelationManager extends RelationManager
{
    protected static string $relationship = 'teamMembers';
    public function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('participant'); 
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // 
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('participant.full_name')
            ->columns([
                Tables\Columns\TextColumn::make('participant.full_name')
                    ->label('Nama Anggota')
                    ->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->label('Peran')
                    ->badge(),
                Tables\Columns\TextColumn::make('participant.nisn')
                    ->label('NISN'),
            ])
            ->filters([
                //
            ])
            ->headerActions([

            ])
            ->actions([
                Action::make('Lihat Detail')
                    ->icon('heroicon-o-eye')
                    ->infolist(function (Infolist $infolist, TeamMember $record) {
                        // Pastikan kita punya participant sebelum membuat schema
                        if (!$record->participant) {
                            return $infolist->schema([]);
                        }
                        return $infolist
                            ->record($record->participant) 
                            ->schema([
                                Infolists\Components\Section::make('Data Detail Peserta')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('full_name'),
                                        Infolists\Components\TextEntry::make('nisn')->label('NISN'),
                                        Infolists\Components\TextEntry::make('place_of_birth')->label('Tempat Lahir'),
                                        Infolists\Components\TextEntry::make('date_of_birth')->label('Tanggal Lahir')->date(),
                                        Infolists\Components\TextEntry::make('phone_number')->label('No. Telepon'),
                                        Infolists\Components\TextEntry::make('address')->label('Alamat')->columnSpanFull(),
                                        
                                        // 3. Sesuaikan nama koleksi dengan yang ada di Model Participant Anda
                                        Infolists\Components\SpatieMediaLibraryImageEntry::make('student_proofs')
                                            ->label('Kartu Pelajar/Mahasiswa')
                                            ->collection('student-proofs'),
                                        Infolists\Components\SpatieMediaLibraryImageEntry::make('twibbon_proofs')
                                            ->label('Bukti Twibbon')
                                            ->collection('twibbon-proofs'),
                                    ])->columns(2),
                            ]);
                    })
                    ->modalSubmitAction(false) 
                    ->modalCancelActionLabel('Tutup'),
            ])
            ->bulkActions([]);
    }
}
