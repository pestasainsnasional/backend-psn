<?php

namespace App\Filament\Resources\TeamResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Tables\Actions\Action;

class TeamMembersRelationManager extends RelationManager
{
    protected static string $relationship = 'teamMembers';

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
                // Menarik nama lengkap dari relasi 'participant' di model TeamMember
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
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // INI ADALAH TOMBOL UNTUK MELIHAT DETAIL DALAM MODAL
                Action::make('Lihat Detail')
                    ->icon('heroicon-o-eye')
                    ->infolist(function (Infolist $infolist, $record) {
                        return $infolist
                            ->record($record->participant) // Menggunakan data dari participant
                            ->schema([
                                Infolists\Components\Section::make('Data Detail Peserta')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('full_name'),
                                        Infolists\Components\TextEntry::make('nisn')->label('NISN'),
                                        Infolists\Components\TextEntry::make('place_of_birth')->label('Tempat Lahir'),
                                        Infolists\Components\TextEntry::make('date_of_birth')->label('Tanggal Lahir')->date(),
                                        Infolists\Components\TextEntry::make('phone_number')->label('No. Telepon'),
                                        Infolists\Components\TextEntry::make('address')->label('Alamat')->columnSpanFull(),
                                        Infolists\Components\SpatieMediaLibraryImageEntry::make('identity_card')
                                            ->label('Kartu Identitas')
                                            ->collection('identity-cards'),
                                    ])->columns(2),
                            ]);
                    })
                    ->modalSubmitAction(false) 
                    ->modalCancelActionLabel('Tutup'),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
