<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrinterTask extends Model
{
    protected $fillable=[
        'printer_id',
        'ip_address',
        'device_name',
        'device_uuid',
       
    ];
    
}
