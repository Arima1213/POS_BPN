<?php

use App\Http\Controllers\GeneralLedgerPrintController;
use App\Http\Controllers\TransactionPrintController;
use Illuminate\Support\Facades\Route;

// routes/web.php
Route::get('/transactions/{transaction}/download-pdf', [TransactionPrintController::class, 'download'])->name('transactions.download.pdf');
Route::get('/transactions/{transaction}/print-receipt', [TransactionPrintController::class, 'printStruk'])->name('transactions.print.receipt');

Route::get('/general-ledger/download-pdf', [GeneralLedgerPrintController::class, 'download'])
    ->name('general-ledger.download.pdf');


Route::get('/', function () {
    return view('welcome');
});
