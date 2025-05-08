<?php

use App\Http\Controllers\BalanceSheetExportController;
use App\Http\Controllers\EquityReportController;
use App\Http\Controllers\GeneralLedgerPrintController;
use App\Http\Controllers\Owner\IncomeStatementExportController;
use App\Http\Controllers\TransactionPrintController;
use App\Http\Controllers\TrialBalanceExportController;
use Illuminate\Support\Facades\Route;

// routes/web.php
Route::get('/transactions/{transaction}/download-pdf', [TransactionPrintController::class, 'download'])->name('transactions.download.pdf');
Route::get('/transactions/{transaction}/print-receipt', [TransactionPrintController::class, 'printStruk'])->name('transactions.print.receipt');

Route::get('/general-ledger/download-pdf', [GeneralLedgerPrintController::class, 'download'])
    ->name('general-ledger.download.pdf');

Route::get('/trial-balance/export-pdf', [TrialBalanceExportController::class, 'download'])->name('trial-balance.export.pdf');

Route::get('/balance-sheet/export-pdf', [BalanceSheetExportController::class, 'download'])->name('balance-sheet.export.pdf');

Route::get('/owner/reports/equity/download', [EquityReportController::class, 'download'])->name('owner.equity.download.pdf');

Route::get('/owner/reports/income-statement/download', [IncomeStatementExportController::class, 'download'])->name('owner.income-statement.download.pdf');

Route::get('/', function () {
    return redirect('/kasir/login');
});