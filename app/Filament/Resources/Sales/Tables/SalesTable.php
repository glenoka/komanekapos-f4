<?php

namespace App\Filament\Resources\Sales\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SalesTable
{
    public static function configure(Table $table): Table
    {
        return $table
        ->defaultSort('sale_date','desc')
            ->columns([
                TextColumn::make('invoice_number')
                ->toggleable()
                    ->searchable(),
                TextColumn::make('customer_id')
                    ->numeric()
                    ->toggleable(true)
                    ->sortable(),
                TextColumn::make('sale_date')
                    ->dateTime()
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('table_no')
                    ->searchable(),
                TextColumn::make('total_amount')
                    ->numeric()
                    ->prefix('IDR')
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.'))
                    ->sortable(),
                TextColumn::make('activity'),
                TextColumn::make('status'),
                TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                
            ])
            ->filters([
                //
            ])
            ->recordActions([
              
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
