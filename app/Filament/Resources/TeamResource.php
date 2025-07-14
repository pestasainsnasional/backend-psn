<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamResource\Pages;
use App\Models\Team;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama Tim')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('school_name')->label('Nama Sekolah')->searchable(),
                Tables\Columns\TextColumn::make('companion_teacher_name')->label('Guru Pendamping'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
}