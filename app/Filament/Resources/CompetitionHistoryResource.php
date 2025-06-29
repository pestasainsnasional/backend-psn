<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompetitionHistoryResource\Pages;
use App\Models\CompetitionHistory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

class CompetitionHistoryResource extends Resource
{
    protected static ?string $model = CompetitionHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationLabel = 'Riwayat Kompetisi';
    protected static ?string $navigationGroup = 'Data Historis';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('history_id')
                    ->relationship('history', 'season_year')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "PSN " . $record->season_year . " - " . $record->theme)
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Tahun Pesta Sains Nasional (History)'),
                
                Forms\Components\TextInput::make('competition_name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Cabang Lomba'),
                Forms\Components\Select::make('competition_type')
                    ->options([
                        'individu' => 'Individu',
                        'kelompok' => 'Kelompok',
                    ])
                    ->required()
                    ->label('Jenis Lomba'),
                Forms\Components\TextInput::make('winner_name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Pemenang'),
                Forms\Components\TextInput::make('winner_school')
                    ->required()
                    ->maxLength(255)
                    ->label('Asal Sekolah'),
                
                SpatieMediaLibraryFileUpload::make('winner_photo')
                    ->collection('winner_photos')
                    ->label('Foto Pemenang')
                    ->nullable()
                    ->image()
                    ->directory('competition-winners'),
                
                Forms\Components\TextInput::make('rank')
                    ->required()
                    ->maxLength(255)
                    ->label('Peringkat'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('history.season_year')
                    ->sortable()
                    ->searchable()
                    ->label('Tahun PSN')
                    ->formatStateUsing(fn (string $state): string => "PSN {$state}"),
                Tables\Columns\TextColumn::make('competition_name')
                    ->sortable()
                    ->searchable()
                    ->label('Nama Lomba'),
                Tables\Columns\TextColumn::make('winner_name')
                    ->sortable()
                    ->searchable()
                    ->label('Nama Pemenang'),
                Tables\Columns\TextColumn::make('winner_school')
                    ->sortable()
                    ->searchable()
                    ->label('Asal Sekolah'),
                Tables\Columns\TextColumn::make('rank')
                    ->sortable()
                    ->searchable()
                    ->label('Peringkat'),
                
                SpatieMediaLibraryImageColumn::make('winner_photo')
                    ->collection('winner_photos')
                    ->label('Foto')
                    ->circular()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('history_id')
                    ->relationship('history', 'season_year')
                    ->label('Filter Tahun PSN'),
                Tables\Filters\SelectFilter::make('competition_type')
                    ->options([
                        'individu' => 'Individu',
                        'kelompok' => 'Kelompok',
                    ])
                    ->label('Filter Jenis Lomba'),
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
            'index' => Pages\ListCompetitionHistories::route('/'),
            'create' => Pages\CreateCompetitionHistory::route('/create'),
            'edit' => Pages\EditCompetitionHistory::route('/{record}/edit'),
        ];
    }
}