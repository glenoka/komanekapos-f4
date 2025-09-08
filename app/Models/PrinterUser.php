<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrinterUser extends Model
{
    protected $fillable=[
        'printer_id',
        'user_id',
    ];

    public function printer(){
        return $this->belongsTo(Printers::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
