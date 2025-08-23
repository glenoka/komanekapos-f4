<?php

namespace App\Filament\Resources\Printers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PrintersForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('code')
                    ->required(),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                Select::make('connection_type')
                    ->options(['network' => 'Network', 'usb' => 'Usb', 'bluetooth' => 'Bluetooth', 'serial' => 'Serial'])
                    ->default('network')
                    ->required(),
                TextInput::make('ip_address')
                    ->default(null),
                TextInput::make('port')
                    ->required()
                    ->default('9100'),
                TextInput::make('mac_address')
                    ->default(null),
            ]);
    }
}
