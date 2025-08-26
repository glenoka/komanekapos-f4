<?php

namespace App\Filament\Resources\Printers;

use App\Filament\Resources\Printers\Pages\CreatePrinters;
use App\Filament\Resources\Printers\Pages\EditPrinters;
use App\Filament\Resources\Printers\Pages\ListPrinters;
use App\Filament\Resources\Printers\Schemas\PrintersForm;
use App\Filament\Resources\Printers\Tables\PrintersTable;
use App\Models\Printers;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PrintersResource extends Resource
{
    protected static ?string $model = Printers::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PrintersForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrintersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PrinterTaskRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrinters::route('/'),
            'create' => CreatePrinters::route('/create'),
            'edit' => EditPrinters::route('/{record}/edit'),
        ];
    }
}
