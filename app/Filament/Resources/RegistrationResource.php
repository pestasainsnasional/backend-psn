<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Registration;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RegistrationResource\Pages;
use App\Filament\Resources\RegistrationResource\RelationManagers;
use App\Filament\Resources\TeamResource\RelationManagers\TeamMembersRelationManager;
use Filament\Tables\Filters\SelectFilter;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class RegistrationResource extends Resource
{
    protected static ?string $model = Registration::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Manajemen Pendaftaran';
    protected static ?string $navigationLabel = 'Registrasi';
    protected static ?int $navigationSort = 1; 

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Membuat form dengan relasi
                Forms\Components\Select::make('participant_id')
                    ->relationship('participant', 'full_name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Peserta (Kontak Utama)'),

                Forms\Components\Select::make('competition_id')
                    ->relationship('competition', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Kompetisi yang Diikuti'),

                Forms\Components\Select::make('team_id')
                    ->relationship('team', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Tim'),
                
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'verified' => 'Verified',
                        'rejected' => 'Rejected',
                        'draft' => 'Draft',
                    ])
                    ->required()
                    ->default('pending'),
            ]);
    }


    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Pendaftaran')
                    ->schema([
                        Infolists\Components\TextEntry::make('team.name')->label('Nama Tim'),
                        Infolists\Components\TextEntry::make('competition.name')->label('Kompetisi'),
                        Infolists\Components\TextEntry::make('participant.full_name')->label('Didaftarkan Oleh'),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->colors([
                                'warning' => 'pending',
                                'success' => 'verified',
                                'danger'  => 'rejected',
                                'gray'    => 'draft',
                            ]),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Tanggal Daftar')
                            ->dateTime('d M Y, H:i'),
                    ])->columns(2),
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                
                Tables\Columns\TextColumn::make('team.name')
                    ->label('Nama Tim')
                    ->searchable()
                    ->sortable(),

            
                Tables\Columns\TextColumn::make('competition.name')
                    ->label('Kompetisi')
                    ->searchable(),

            
                Tables\Columns\TextColumn::make('participant.full_name')
                    ->label('Didaftarkan Oleh')
                    ->searchable(),
                
            
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'verified',
                        'danger' => 'rejected',
                        'gray' => 'draft',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Daftar')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'verified' => 'Verified',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Lihat Detail'),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            TeamMembersRelationManager::class
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegistrations::route('/'),
            'create' => Pages\CreateRegistration::route('/create'),
            'view' => Pages\ViewRegistration::route('/{record}'), 
            'edit' => Pages\EditRegistration::route('/{record}/edit'),
        ];
    }    
}