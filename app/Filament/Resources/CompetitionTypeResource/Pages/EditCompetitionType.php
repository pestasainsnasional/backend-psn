<?php

namespace App\Filament\Resources\CompetitionTypeResource\Pages;

use App\Filament\Resources\CompetitionTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompetitionType extends EditRecord
{
    protected static string $resource = CompetitionTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
