<?php

namespace App\Filament\Resources\CompetitionTypeResource\Pages;

use App\Filament\Resources\CompetitionTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCompetitionTypes extends ListRecords
{
    protected static string $resource = CompetitionTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
