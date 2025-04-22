<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transactions;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionPrintController extends Controller
{
    public function download(Transactions $transaction)
    {
        $pdf = Pdf::loadView('pdf.transaction', ['transaction' => $transaction]);
        return $pdf->download("Invoice_{$transaction->code}.pdf");
    }

    public function printReceipt(Transactions $transaction)
    {
        return view('print.receipt', compact('transaction'));
    }
}
