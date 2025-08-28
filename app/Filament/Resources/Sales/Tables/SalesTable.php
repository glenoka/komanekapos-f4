<?php

namespace App\Filament\Resources\Sales\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Colors\Color;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;

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
                TextColumn::make('customer.name')
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
                    ->prefix('IDR ')
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.'))
                    ->sortable(),
                TextColumn::make('activity')
                 ->badge()
    ->color(fn (string $state): string => match ($state) {
        default => 'primary',
    })

                ->getStateUsing(function ($record) {
                    return ucfirst($record->activity);
                }),
                TextColumn::make('status'),
                TextColumn::make('user.name')
                ->label('Created By')
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
