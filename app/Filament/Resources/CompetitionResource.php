<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompetitionResource\Pages;
use App\Models\Competition;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;


class CompetitionResource extends Resource
{
    protected static ?string $model = Competition::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationLabel = 'Kompetisi';
    protected static ?string $navigationGroup = 'Kompetisi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('competition_logo')
                            ->collection('competition_logo')
                            ->label('Logo Kompetisi')
                            ->image()
                            ->imageEditor()
                            ->responsiveImages()
                            ->columnSpanFull(),

                        Forms\Components\Select::make('competition_type_id')
                            ->relationship('competitionType', 'type')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Tipe Kompetisi'),

                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nama Kompetisi'),

                        Forms\Components\TextInput::make('major')
                            ->maxLength(255)
                            ->label('Jurusan'),
                        Forms\Components\TextInput::make('guidebook_link')
                            ->maxLength(255)
                            ->label('Link Guidebook'),
                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->default(true)
                            ->label('Aktifkan Kompetisi'),

                        Forms\Components\RichEditor::make('rules')
                            ->label('Aturan Kompetisi')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('competition_logo')
                    ->collection('competition_logo')
                    ->label('Foto')
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Kompetisi')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('competitionType.type')
                    ->label('Tipe')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('major')
                    ->label('Jurusan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('guidebook_link')
                    ->label('Link Guidebook')
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status Aktif')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Status Aktif')
                    ->options([
                        true => 'Aktif',
                        false => 'Tidak Aktif',
                    ]),
                Tables\Filters\SelectFilter::make('competition_type_id')
                    ->label('Tipe Kompetisi')
                    ->relationship('competitionType', 'type')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
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
            'index' => Pages\ListCompetitions::route('/'),
            'create' => Pages\CreateCompetition::route('/create'),
            'edit' => Pages\EditCompetition::route('/{record}/edit'),
        ];
    }
}
