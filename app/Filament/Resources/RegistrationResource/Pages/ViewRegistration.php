<?php

namespace App\Filament\Resources\RegistrationResource\Pages;

use App\Filament\Resources\RegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRegistration extends ViewRecord
{
    /**
     * Menghubungkan halaman ini kembali ke Resource utamanya.
     */
    protected static string $resource = RegistrationResource::class;

    /**   
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}