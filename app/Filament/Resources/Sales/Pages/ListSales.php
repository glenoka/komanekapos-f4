<?php

namespace App\Filament\Resources\Sales\Pages;

use App\Filament\Resources\Sales\SalesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;


class ListSales extends ListRecords
{
    protected static string $resource = SalesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
    public function getTabs(): array
{
    return [
        'all' => Tab::make(),
        'active' => Tab::make(),
        'inactive' => Tab::make(),
    ];
}
}
