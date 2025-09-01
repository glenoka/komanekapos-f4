<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\Sales;
use Filament\Widgets\Widget;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;


   class MonthlySalesStats extends Widget
{
    protected string $view = 'filament.widgets.monthly-sales-stats';


    public int $month;
    public int $year;

   public function mount(?int $month = null, ?int $year = null): void
{
    $this->month = $month ?? now()->month;
    $this->year  = $year ?? now()->year;
}

    public function getCurrentTotal(): float
    {
        return SaleS::query()
            ->whereYear('sale_date', $this->year)
            ->whereMonth('sale_date', $this->month)
            ->sum('total_amount');
    }

    public function getPreviousTotal(): float
    {
        $previousDate = Carbon::create($this->year, $this->month, 1)->subMonth();

        return Sales::query()
            ->whereYear('sale_date', $previousDate->year)
            ->whereMonth('sale_date', $previousDate->month)
            ->sum('total_amount');
    }

    public function getPercentageDifference(): float
    {
        $current  = $this->getCurrentTotal();
        $previous = $this->getPreviousTotal();

        if ($previous <= 0) {
            return 100; // default kalau bulan lalu nol
        }

        return round((($current - $previous) / $previous) * 100, 2);
    }
}
