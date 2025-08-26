<?php

namespace App\Filament\Resources\Printers\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PrinterTaskRelationManager extends RelationManager
{
    protected static string $relationship = 'printer_task';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('device_name')
                ->required()
                ->maxLength(255),
            
          
                TextInput::make('device_uuid')
                ->required()
                ->maxLength(255)
                ->default(fn () => (string) \Illuminate\Support\Str::uuid()),
            
            TextInput::make('ip_address')
                ->required()
                ->default(fn () => request()->ip())
                ->maxLength(255),
            
            ]);
    }

   
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('device_name')
                    ->searchable(),
                    TextColumn::make('ip_address')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
