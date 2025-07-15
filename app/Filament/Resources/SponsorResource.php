<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SponsorResource\Pages;
use App\Models\Sponsor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SponsorResource extends Resource
{
    protected static ?string $model = Sponsor::class;
    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationGroup = 'Others';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nama Brand')
                    ->required(),
                SpatieMediaLibraryFileUpload::make('logo')
                    ->label('Logo Sponsor')
                    ->collection('logo sponsor')
                    ->image()
                    ->required(),
                TextArea::make('description')
                    ->label('Deskripsi Brand')
                    ->columnSpanFull()
                    ->required(),
                Select::make('type')
                    ->options([
                        'sponsorship' => 'Sponsorship',
                        'media_partner' => 'Media Partner'
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('logo')
                    ->collection('logo sponsor')
                    ->label('Logo'),
                TextColumn::make('name')
                    ->label('Nama Brand')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->html(),
                TextColumn::make('type')
                    ->label('Tipe Sponsor')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'sponsorship' => 'Sponsorship',
                            'media_partner' => 'Media Partner',
                            default => ucfirst(str_replace('_', ' ', $state)),
                        };
                    }),

            ])
            ->filters([])
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSponsors::route('/'),
            'create' => Pages\CreateSponsor::route('/create'),
            'edit' => Pages\EditSponsor::route('/{record}/edit'),
        ];
    }
}
