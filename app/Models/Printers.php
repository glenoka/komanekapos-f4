<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Printers extends Model
{
    protected $fillable=[
        'name',
        'code',
        'description',
        'connection_type',
        'ip_address',
        'port',
        'mac_address'
    ];

    public function printer_task():HasMany
    {
        return $this->hasMany(PrinterTask::class,'printer_id','id');
    }
}
