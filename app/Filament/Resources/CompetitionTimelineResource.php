<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompetitionTimelineResource\Pages;
use App\Models\CompetitionTimeline;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CompetitionTimelineResource extends Resource
{
    protected static ?string $model = CompetitionTimeline::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Timeline Kompetisi';
    protected static ?string $navigationGroup = 'Kompetisi';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('competition_id')
                ->relationship('competition', 'name')
                ->required(),
            Forms\Components\TextInput::make('title')
                ->required()
                ->maxLength(255),
            Forms\Components\DatePicker::make('start_date')
                ->required(),
            Forms\Components\DatePicker::make('end_date')
                ->required(),
            Forms\Components\Textarea::make('description')
                ->maxLength(1000),
            Forms\Components\TextInput::make('order')
                ->numeric()
                ->default(1)
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('competition.name')->label('Lomba')->sortable(),
            Tables\Columns\TextColumn::make('title'),
            Tables\Columns\TextColumn::make('start_date')->date(),
            Tables\Columns\TextColumn::make('end_date')->date(),
            Tables\Columns\TextColumn::make('order'),
        ])
            ->defaultSort('order')
            ->filters([
                Tables\Filters\SelectFilter::make('competition_id')
                    ->label('Filter Lomba')
                    ->relationship('competition', 'name')
                    ->searchable()
            ])

            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompetitionTimelines::route('/'),
            'create' => Pages\CreateCompetitionTimeline::route('/create'),
            'edit' => Pages\EditCompetitionTimeline::route('/{record}/edit'),
        ];
    }
}
