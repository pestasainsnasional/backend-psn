<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HistoryResource\Pages;
use App\Models\History;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

class HistoryResource extends Resource
{
    protected static ?string $model = History::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Manajemen Event';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('theme')
                    ->required()
                    ->maxLength(255)
                    ->label('Tema Pesta Sains Nasional'),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->maxLength(65535)
                    ->rows(5)
                    ->label('Deskripsi Ringkasan'),
                Forms\Components\TextInput::make('season_year')
                    ->required()
                    ->numeric()
                    ->integer()
                    ->minValue(2003)
                    ->maxValue(date('Y') + 5)
                    ->label('Tahun Penyelenggaraan'),
                Forms\Components\TextInput::make('total_participants')
                    ->required()
                    ->numeric()
                    ->integer()
                    ->minValue(0)
                    ->label('Total Peserta'),
                Forms\Components\TextInput::make('total_competitions')
                    ->required()
                    ->numeric()
                    ->integer()
                    ->minValue(0)
                    ->label('Total Cabang Lomba'),

                SpatieMediaLibraryFileUpload::make('documentation')
                    ->collection('documentation')
                    ->label('Dokumentasi (Foto/Video Galeri Utama)')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'video/mp4', 'video/webm'])
                    ->nullable()
                    ->multiple()
                    ->reorderable()
                    ->downloadable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('theme')
                    ->searchable()
                    ->sortable()
                    ->label('Tema'),
                Tables\Columns\TextColumn::make('season_year')
                    ->searchable()
                    ->sortable()
                    ->label('Tahun')
                    ->formatStateUsing(fn(string $state): string => "PSN {$state}")
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('total_participants')
                    ->numeric()
                    ->sortable()
                    ->label('Peserta'),
                Tables\Columns\TextColumn::make('total_competitions')
                    ->numeric()
                    ->sortable()
                    ->label('Lomba'),

                SpatieMediaLibraryImageColumn::make('documentation')
                    ->collection('documentation')
                    ->label('Galeri Utama')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->limit(3)
                    ->height('100px')
                    ->wrap(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Dibuat Pada'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Diperbarui Pada'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('season_year')
                    ->options(fn(): array => History::select('season_year')->distinct()->orderBy('season_year', 'desc')->pluck('season_year', 'season_year')->toArray())
                    ->label('Filter Tahun'),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHistories::route('/'),
            'create' => Pages\CreateHistory::route('/create'),
            'edit' => Pages\EditHistory::route('/{record}/edit'),
        ];
    }
}
