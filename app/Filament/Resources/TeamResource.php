<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamResource\Pages;
use App\Filament\Resources\TeamResource\RelationManagers;
use App\Models\Team;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

   
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Manajemen Pendaftaran';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nama Tim'),

                        Forms\Components\TextInput::make('school_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nama Sekolah'),

                        Forms\Components\TextInput::make('school_email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->label('Email Sekolah'),

                        Forms\Components\TextInput::make('npsn')
                            ->required()
                            ->numeric() 
                            ->label('NPSN Sekolah'),
                    ])
                    ->columns(2), 

                
                Forms\Components\Section::make('Informasi Guru Pendamping')
                    ->schema([
                        Forms\Components\TextInput::make('companion_teacher_name')
                            ->required()
                            ->label('Nama Guru'),
                        
                        Forms\Components\TextInput::make('companion_teacher_contact')
                            ->tel() 
                            ->prefix('+62') 
                            ->required()
                            ->label('Kontak Guru'),

                        Forms\Components\TextInput::make('companion_teacher_nip')
                            ->required()
                            ->numeric()
                            ->label('NIP Guru'),
                    ])
                    ->columns(2),
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
                    ->searchable()
                    ->sortable()
                    ->limit(30), 

                Tables\Columns\TextColumn::make('companion_teacher_name')
                    ->label('Guru Pendamping')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Daftar')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            // Nanti kita tambahin relasi ke anggota tim di sini
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeams::route('/'),
            'create' => Pages\CreateTeam::route('/create'),
            'edit' => Pages\EditTeam::route('/{record}/edit'),
        ];
    }
}
