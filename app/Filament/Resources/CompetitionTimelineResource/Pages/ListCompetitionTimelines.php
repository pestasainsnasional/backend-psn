<?php

namespace App\Filament\Resources\CompetitionTimelineResource\Pages;

use App\Filament\Resources\CompetitionTimelineResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCompetitionTimelines extends ListRecords
{
    protected static string $resource = CompetitionTimelineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
