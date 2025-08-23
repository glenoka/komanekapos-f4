<?php

namespace App\Filament\Resources\Printers\Pages;

use App\Filament\Resources\Printers\PrintersResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrinters extends ListRecords
{
    protected static string $resource = PrintersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
