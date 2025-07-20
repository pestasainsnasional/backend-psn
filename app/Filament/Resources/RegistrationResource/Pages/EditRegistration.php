<?php

namespace App\Filament\Resources\RegistrationResource\Pages;

use App\Filament\Resources\RegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRegistration extends EditRecord
{
    protected static string $resource = RegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

     protected function mutateFormDataBeforeFill(array $data): array
    {
     
        $registration = $this->getRecord()->load(['team', 'competition']);
        $data['team.name'] = $registration->team?->name;
        $data['competition.name'] = $registration->competition?->name;
        
        return $data;
    }
}
