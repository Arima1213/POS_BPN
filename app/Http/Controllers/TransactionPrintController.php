<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transactions;
use Barryvdh\DomPDF\Facade\Pdf;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

class TransactionPrintController extends Controller
{
    public function download(Transactions $transaction)
    {
        $pdf = Pdf::loadView('pdf.transaction', ['transaction' => $transaction]);
        return $pdf->download("Invoice_{$transaction->code}.pdf");
    }

    public function printStruk($id)
    {
        $transaksi = Transactions::with('details')->findOrFail($id);

        // Ganti "POS-58" dengan nama printer yang kamu pakai di Windows (lihat di Control Panel > Devices and Printers)
        $connector = new WindowsPrintConnector("POS-58");

        $printer = new Printer($connector);

        // Header
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("PT BERKAH PADI NUSANTARA\n");
        $printer->text("Jl. Contoh No.1\n");
        $printer->text("==============================\n");

        // Info transaksi
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("Kode   : " . $transaksi->code . "\n");
        $printer->text("Tanggal: " . $transaksi->created_at->format('d M Y H:i') . "\n");
        $printer->text("------------------------------\n");

        // Item
        foreach ($transaksi->details as $item) {
            $printer->text(str_pad($item->name, 16));
            $printer->text(str_pad($item->quantity, 3, ' ', STR_PAD_LEFT) . "x");
            $printer->text(str_pad(number_format($item->price, 0, '', '.'), 7, ' ', STR_PAD_LEFT) . "\n");

            $subtotal = number_format($item->subtotal, 0, '', '.');
            $printer->text("      Rp" . str_pad($subtotal, 10, ' ', STR_PAD_LEFT) . "\n");
        }

        $printer->text("------------------------------\n");

        // Total, bayar, kembali
        $printer->text("Total   : Rp " . number_format($transaksi->total, 0, ',', '.') . "\n");
        $printer->text("Bayar   : Rp " . number_format($transaksi->paid_amount, 0, ',', '.') . "\n");
        $printer->text("Kembali : Rp " . number_format($transaksi->change_amount, 0, ',', '.') . "\n");

        // Footer
        $printer->text("==============================\n");
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("Terima kasih telah berbelanja!\n");

        $printer->feed(3);
        $printer->cut();
        $printer->close();

        return redirect()->back()->with('success', 'Struk berhasil dicetak!');
    }
}
