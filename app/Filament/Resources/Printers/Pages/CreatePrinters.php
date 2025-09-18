<?php

namespace App\Filament\Resources\Printers\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Printers\PrintersResource;

class CreatePrinters extends CreateRecord
{
    protected static string $resource = PrintersResource::class;
    protected function afterCreate(): void
    {
        $data = $this->form->getState();
        $printername = preg_replace('/[^A-Za-z0-9]/', '_', $data['name']);
        $cmd = sprintf(
            'lpadmin -p %s -E -v socket://%s:%s -m raw',
            escapeshellarg($printername),
            escapeshellarg($data['ip_address']),
            escapeshellarg($data['port'])
        );
        exec($cmd);
    
        // Aktifkan printer
        exec("cupsenable " . escapeshellarg($printername));
        exec("cupsaccept " . escapeshellarg($printername));
    
      
    }
}
