<?php

namespace App\Filament\Resources\Sales\Pages;

use Carbon\Carbon;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\Sales\SalesResource;


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
            // Tab untuk data penjualan hari ini
            'today' => Tab::make('Today')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->whereDate('sale_date', Carbon::today());
                }),

            // Tab untuk data penjualan kemarin
            'yesterday' => Tab::make('Yesterday')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->whereDate('sale_date', Carbon::yesterday());
                }),

            // Tab untuk semua data penjualan (tanpa filter tanggal)
            'all' => Tab::make('All')
                ->modifyQueryUsing(function (Builder $query) {
                    // Tidak perlu menambahkan kondisi 'whereDate' untuk menampilkan semua data.
                    // Atau, jika Anda memiliki filter default lain, Anda bisa menghapusnya di sini.
                }),
        ];
}
}
