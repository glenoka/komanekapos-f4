<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Sales;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;


class DailyReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */protected $month;
    protected $year;

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }
    public function collection()
    {
        $report = Sales::query()
        ->whereMonth('sale_date', $this->month)
        ->whereYear('sale_date', $this->year)
        ->selectRaw('DATE(sale_date) as date, SUM(total_amount) as total, SUM(tax_amount) as tax')
        ->groupByRaw('DATE(sale_date)')
        ->get();
    
        return $report;
    }
    public function headings(): array
    {
        return ['Tanggal', 'Total Penjualan', 'Pajak'];
    }

    public function map($row): array
    {
        return [
            $row->date,
            $row->total, // tetap nilai asli
            $row->tax,   // tetap nilai asli
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' =>  NumberFormat::FORMAT_DATE_DDMMYYYY,
            'B' => '"Rp." #,##0_-',
        'C' => '"Rp." #,##0_-',
        ];
    }

    protected function formatRupiah($value): string
    {
        return 'Rp. ' . number_format($value, 0, ',', '.');
    }

    public function title(): string
    {
        return 'Laporan Harian';
    }
    
}
