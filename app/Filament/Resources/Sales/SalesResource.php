<?php

namespace App\Filament\Resources\Sales;

use App\Filament\Resources\Sales\Pages\CreateSales;
use App\Filament\Resources\Sales\Pages\EditSales;
use App\Filament\Resources\Sales\Pages\ListSales;
use App\Filament\Resources\Sales\Pages\ViewSales;
use App\Filament\Resources\Sales\Schemas\SalesForm;
use App\Filament\Resources\Sales\Schemas\SalesInfolist;
use App\Filament\Resources\Sales\Tables\SalesTable;
use App\Models\Sales;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SalesResource extends Resource
{
    protected static ?string $model = Sales::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'invoice_number';

    public static function form(Schema $schema): Schema
    {
        return SalesForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SalesInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SalesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSales::route('/'),
            'create' => CreateSales::route('/create'),
          
            'edit' => EditSales::route('/{record}/edit'),
        ];
    }
}
