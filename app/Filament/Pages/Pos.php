<?php

namespace App\Filament\Pages;

use App\Models\Printers;
use Filament\Pages\Page;

class Pos extends Page

{
    protected string $view = 'filament.pages.pos';
    public $printerStatus = "disconnected";


    
    public function mount(){
        $ip = request()->ip();
        $printer = Printers::whereHas('printer_task', function ($q) use ($ip) {
            $q->where('ip_address', $ip);
        })->first();
        $ipPrinter = $printer->ip_address??null;

        if ($printer) {
            $ipPrinter = $printer->ip_address;
            $printerName = $printer->name;


            // Cek apakah status printer sudah di-cache di session
            // Selalu update status printer terbaru
            $isReachable = $this->isReachable($ipPrinter);

            // Simpan/update session dengan status terbaru
            session()->put('status_printer', $isReachable);
            session()->put('printernya','aada');

            session()->put("printer_{$ipPrinter}_last_checked", now());

            $this->printerStatus = $isReachable ? "online" : "disconnected";
        }
    }

     function isReachable($ip, $port = 9100, $timeout = 2)
    {
        $conn = @fsockopen($ip, $port, $errno, $errstr, $timeout);
        if ($conn) {
            fclose($conn);
            return true;
        }
        return false;
    }
}
