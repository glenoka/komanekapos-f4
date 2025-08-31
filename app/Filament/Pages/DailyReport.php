<?php

namespace App\Filament\Pages;

use Carbon\Carbon;
use App\Models\Sales;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use App\Exports\DailyReportExport;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;




class DailyReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationLabel = 'Daily Sales Report';
    protected string $view = 'filament.pages.daily-report';

    public ?string $month = null;


    public $selectedMonth;
    public $selectedYear;
    public array $formData = [];

    public function updateCalendar(): void
    {
        $data = $this->form->getState(); // ambil semua nilai dari form

        $this->selectedMonth = $data['selectedMonth'];
        $this->selectedYear = $data['selectedYear'];

        // Generate ulang kalender
        $this->getCalendarDataProperty();
    }

    protected function getFormSchema(): array
    {

        return [
            Section::make('Filter Periode')
                ->columns(1)
                ->schema([
                    Grid::make(3)
                        ->schema([
                            Select::make('selectedMonth')
                                ->label('Month')
                                ->default(now()->month)
                                ->options(
                                    collect(range(1, 12))->mapWithKeys(function ($month) {
                                        return [$month => Carbon::create()->month($month)->translatedFormat('F')];
                                    })->toArray()
                                )
                                ->required(),

                            Select::make('selectedYear')
                                ->label('Year')
                                ->default(now()->year)
                                ->options(
                                    collect(range(now()->year, now()->year - 5))->mapWithKeys(fn($year) => [$year => $year])->toArray()
                                )
                                ->required(),

                            // Action dipindah ke baris baru agar lebih semantik
                            Actions::make([
                                Action::make('submit')
                                    ->label('Tampilkan')
                                    ->action('updateCalendar')
                                    ->color('primary')
                                    ->icon('heroicon-m-eye'),
                                Action::make('export')
                                    ->label('Export Excel')
                                    ->action('exportExcel')
                                    ->color('success')
                                    ->icon('heroicon-m-eye')
                            ])->columnSpanFull(),

                           
                        ]),
                ])
        ];
    }


    public function getCalendarDataProperty(): Collection
    {
        $now = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1);

        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // Ambil data penjualan
        $sales = Sales::whereBetween('sale_date', [$startOfMonth, $endOfMonth])
            ->selectRaw('DATE(sale_date) as date, SUM(total_amount) as total, SUM(tax_amount) as tax')
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        // Buat array kalender
        $daysInMonth = $now->daysInMonth;
        $firstDayOfWeek = $startOfMonth->dayOfWeekIso; // 1 = Monday

        $calendar = collect();
        $week = [];

        // Isi hari kosong di awal minggu (jika tidak mulai Senin)
        for ($i = 1; $i < $firstDayOfWeek; $i++) {
            $week[] = null;
        }

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::createFromDate($now->year, $now->month, $day);
            $data = $sales->get($date->toDateString());

            $week[] = [
                'date' => $date,
                'total' => $data->total ?? 0,
                'tax' => $data->tax ?? 0,
            ];

            // Jika minggu lengkap, push ke calendar
            if (count($week) === 7) {
                $calendar->push($week);
                $week = [];
            }
        }

        // Push minggu terakhir (jika tidak penuh)
        if (!empty($week)) {
            while (count($week) < 7) {
                $week[] = null;
            }
            $calendar->push($week);
        }

        return $calendar;
    }

    public function exportExcel()
{
    $filename = 'daily_report_' . now()->format('Y_m_d_His') . '.xlsx';
    return Excel::download(new DailyReportExport($this->selectedMonth, $this->selectedYear), $filename);
}
}
