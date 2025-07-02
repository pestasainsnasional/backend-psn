<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompetitionTypeResource\Pages;
use App\Models\CompetitionType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CompetitionTypeResource extends Resource
{
    protected static ?string $model = CompetitionType::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Jenis Kompetisi';
    protected static ?string $navigationGroup = 'Kompetisi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->options([
                        'individu' => 'Individu',
                        'group-2-orang'      => 'Group 2 Orang',
                        'group-3-orang'      => 'Group 3 Orang',
                    ])
                    ->required(),
                Forms\Components\Select::make('current_batch')
                    ->options([
                        'pre-sale-1' => 'Pre-Sale 1',
                        'pre-sale-2' => 'Pre-Sale 2',
                        'regular'    => 'Regular',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('slot_remaining')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->default(0.00)
                    ->prefix('Rp'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Created By')
                    ->searchable()
                    ->sortable()
                    ->placeholder('N/A'),

                Tables\Columns\TextColumn::make('type')->searchable(),

                Tables\Columns\TextColumn::make('current_batch')
                    ->label('Batch')
                    ->badge()
                    ->searchable()
                    ->colors([
                        'primary' => 'regular',
                        'success' => 'pre-sale-1',
                        'warning' => 'pre-sale-2',
                    ]),

                Tables\Columns\TextColumn::make('slot_remaining')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('price')->money('IDR')->sortable(),
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
            'index'  => Pages\ListCompetitionTypes::route('/'),
            'create' => Pages\CreateCompetitionType::route('/create'),
            'edit'   => Pages\EditCompetitionType::route('/{record}/edit'),
        ];
    }
}