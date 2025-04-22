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
        $printer->text("Jl. Gajah Mada, Desa Siwalan, Kec. Panceng, Kab. Gresik\n");
        $printer->text("==============================\n");

        // Info transaksi
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("Kode   : " . $transaksi->code . "\n");
        $printer->text("Tanggal: " . $transaksi->created_at->format('d M Y H:i') . "\n");
        $printer->text("------------------------------\n");

        foreach ($transaksi->details as $items) {
            $name = '';
            $price = 0;
            $subtotal = 0;
            $label = '';

            if ($items->item_type === 'product' && $items->product) {
                $name = $items->product->name;
                $price = $items->price;
                $subtotal = $items->subtotal;
                $label = ' pcs';
            } elseif ($items->item_type === 'service' && $items->service) {
                $name = $items->service->name;
                $price = $items->price;
                $subtotal = $items->subtotal;
                $label = ' ' . ($items->service->unit->short ?? 'unit');
            }

            // Baris nama barang/jasa
            $printer->text(str_pad($name, 16) . "\n");

            // Baris 2: qty x harga | subtotal
            $left = str_pad($items->quantity, 2, ' ', STR_PAD_LEFT) .  $label . ' x ' . number_format($price, 0, '', '.');
            $right = 'Rp ' . number_format($subtotal, 0, '', '.');
            $printer->text(str_pad($left, 20) . str_pad($right, 12, ' ', STR_PAD_LEFT) . "\n");
        }


        $printer->text("------------------------------\n");

        // Total, bayar, kembali
        $left = "Total";
        $right = 'Rp ' . number_format($transaksi->total, 0, ',', '.');
        $printer->text(str_pad($left, 20) . str_pad($right, 12, ' ', STR_PAD_LEFT) . "\n");

        $left = "Bayar";
        $right = 'Rp ' . number_format($transaksi->paid_amount, 0, ',', '.');
        $printer->text(str_pad($left, 20) . str_pad($right, 12, ' ', STR_PAD_LEFT) . "\n");

        $left = "Kembali";
        $right = 'Rp ' . number_format($transaksi->change_amount, 0, ',', '.');
        $printer->text(str_pad($left, 20) . str_pad($right, 12, ' ', STR_PAD_LEFT) . "\n");

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
