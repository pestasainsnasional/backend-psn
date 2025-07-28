<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamResource\Pages;
use App\Models\Team;
use App\Models\Registration; 
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Filters\TrashedFilter; 
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Database\Eloquent\SoftDeletingScope; 

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Manajemen Pendaftaran';
    protected static ?string $navigationLabel = 'Tim';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Tim & Sekolah')
                    ->schema([
                        Forms\Components\TextInput::make('name')->required()->label('Nama Tim'),
                        Forms\Components\TextInput::make('school_name')->required()->label('Nama Sekolah'),
                        Forms\Components\TextInput::make('school_email')->email()->required()->label('Email Sekolah'),
                        Forms\Components\TextInput::make('npsn')->numeric()->required()->label('NPSN'),
                    ])->columns(2),
                Forms\Components\Section::make('Guru Pendamping')
                    ->schema([
                        Forms\Components\TextInput::make('companion_teacher_name')->label('Nama Guru'),
                        Forms\Components\TextInput::make('companion_teacher_contact')->tel()->label('Kontak Guru'),
                        Forms\Components\TextInput::make('companion_teacher_email')->email()->label('Email Guru'),
                        Forms\Components\TextInput::make('companion_teacher_nip')->label('NIP Guru'),
                    ])->columns(2),

                Forms\Components\Section::make('Dokumen Tim')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('payment_proofs')
                            ->label('Bukti Pembayaran')
                            ->collection('payment-proofs')
                            ->disabled()
                    ])
                    ->visibleOn('edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Tim')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('school_name')
                    ->label('Nama Sekolah')
                    ->searchable(),
                Tables\Columns\TextColumn::make('npsn')
                    ->label('NPSN')
                    ->searchable(),
                Tables\Columns\TextColumn::make('companion_teacher_name')
                    ->label('Guru Pendamping'),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Tanggal Dihapus')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
    
                Tables\Actions\RestoreAction::make()
                    ->label('Restore') 
                    ->hidden(fn (Team $record): bool => !$record->trashed()),
                Tables\Actions\ForceDeleteAction::make()
                    ->label('Hapus Permanen') 
                    ->icon('heroicon-o-x-mark')
                    ->hidden(fn (Team $record): bool => !$record->trashed()),
                Tables\Actions\DeleteAction::make()
                    ->label('Move to trash') 
                    ->icon('heroicon-o-trash')
                    ->hidden(fn (Team $record): bool => $record->trashed()), 
                ActionGroup::make([
                    Action::make('viewPaymentProof')
                        ->label('Lihat Bukti Pembayaran')
                        ->icon('heroicon-o-eye')
                        ->modalContent(fn (Team $record): \Illuminate\Contracts\View\View =>
                            view('filament.modals.view-media', ['media' => $record->getFirstMedia('payment-proofs')]))
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Tutup')
                        ->visible(fn (Team $record): bool => $record->hasMedia('payment-proofs')),

                    Action::make('downloadPaymentProof')
                        ->label('Unduh Bukti Pembayaran')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function (Team $record) {
                            $mediaItem = $record->getFirstMedia('payment-proofs');
                            return $mediaItem ? response()->download($mediaItem->getPath(), $mediaItem->file_name) : null;
                        })
                        ->visible(fn (Team $record): bool => $record->hasMedia('payment-proofs')),
                ])->label('Dokumen')->icon('heroicon-o-document-text'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Move to trash'),
                    Tables\Actions\RestoreBulkAction::make()
                        ->label('Restore Terpilih'),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->label('Hapus Permanen'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeams::route('/'),
            'create' => Pages\CreateTeam::route('/create'),
            'edit' => Pages\EditTeam::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}