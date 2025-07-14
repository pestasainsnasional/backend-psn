<?php
// File: app/Filament/Resources/RegistrationResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\RegistrationResource\Pages;
use App\Models\Registration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RegistrationResource extends Resource
{
    protected static ?string $model = Registration::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Manajemen Pendaftaran';
    protected static ?string $navigationLabel = 'Pendaftaran';

    public static function form(Form $form): Form
    {
        // Form ini digunakan saat mengedit, kita buat simpel saja
        return $form
            ->schema([
                Forms\Components\Section::make('Status Pendaftaran')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'verified' => 'Verified',
                                'rejected' => 'Rejected',
                            ])->required(),
                        Forms\Components\Fieldset::make('Detail Tim')
                            ->schema([
                                Forms\Components\Textarea::make('team.name')->label('Nama Tim')->disabled(),
                                Forms\Components\Textarea::make('competition.name')->label('Kompetisi')->disabled(),
                            ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('team.name')
                    ->label('Nama Tim')
                    ->searchable(),
                Tables\Columns\TextColumn::make('competition.name')
                    ->label('Kompetisi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Didaftarkan Oleh')
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_unique_code')
                    ->label('Kode Pembayaran')
                    ->copyable(),
                // === KOLOM STATUS DIPERBAIKI DI SINI ===
                // Menggunakan BadgeColumn untuk menampilkan status dengan warna
                Tables\Columns\BadgeColumn::make('status')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft_step_1' => 'Draf (Step 1)',
                        'draft_step_2' => 'Draf (Step 2)',
                        'draft_step_3' => 'Draf (Step 3)',
                        'draft_step_4' => 'Menunggu Finalisasi',
                        'pending' => 'Pending',
                        'verified' => 'Terverifikasi',
                        'rejected' => 'Ditolak',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'draft_step_1', 'draft_step_2', 'draft_step_3' => 'info',
                        'draft_step_4' => 'primary',
                        'pending' => 'warning',
                        'verified' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'verified' => 'Verified',
                        'rejected' => 'Rejected',
                    ])
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegistrations::route('/'),
            'edit' => Pages\EditRegistration::route('/{record}/edit'),
            'view' => Pages\ViewRegistration::route('/{record}'),
        ];
    }    
}
