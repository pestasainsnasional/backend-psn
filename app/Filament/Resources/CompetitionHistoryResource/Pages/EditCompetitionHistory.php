<?php

namespace App\Filament\Resources\CompetitionHistoryResource\Pages;

use App\Filament\Resources\CompetitionHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompetitionHistory extends EditRecord
{
    protected static string $resource = CompetitionHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
