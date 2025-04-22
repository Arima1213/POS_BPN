<?php

use App\Http\Controllers\TransactionPrintController;
use Illuminate\Support\Facades\Route;

// routes/web.php
Route::get('/transactions/{transaction}/download-pdf', [TransactionPrintController::class, 'download'])->name('transactions.download.pdf');
Route::get('/transactions/{transaction}/print-receipt', [TransactionPrintController::class, 'printReceipt'])
    ->name('transactions.print.receipt');
Route::get('/', function () {
    return view('welcome');
});
