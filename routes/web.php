<?php

use App\Livewire\Pos;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/receipt/pdf', [Pos::class, 'downloadReceipt1'])
    ->name('receipt.pdf');
