<?php

namespace App\Filament\Resources\Printers\Pages;

use App\Filament\Resources\Printers\PrintersResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPrinters extends EditRecord
{
    protected static string $resource = PrintersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
