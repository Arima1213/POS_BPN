<?php

namespace App\Filament\Resources\HppstatsResource\Widgets;

use App\Models\Product;
use App\Models\Procurement;
use App\Models\ProductStock;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class HppStats extends BaseWidget
{
    protected function getStats(): array
    {
        $now = Carbon::now();

        // Ambil semua produk yang punya pengadaan di luar bulan ini
        $products = Product::with(['stockHistories', 'transactionDetails', 'category'])
            ->whereHas('stockHistories')
            ->get();

        $totalHppFifo = 0;
        $totalHppAverage = 0;
        $totalStokNilai = 0;

        foreach ($products as $product) {
            $stock = ProductStock::where('product_id', $product->id)->first();
            $currentStock = $stock?->current_stock ?? 0;
            if ($currentStock <= 0) {
                continue;
            }

            // FIFO
            $procurements = Procurement::where('product_id', $product->id)
                ->whereMonth('procurement_date', '!=', $now->month)
                ->orderBy('procurement_date', 'asc')
                ->get();

            $remainingQty = $currentStock;
            $fifoHpp = 0;
            foreach ($procurements as $row) {
                if ($remainingQty <= 0) break;

                $takeQty = min($remainingQty, $row->quantity);
                $fifoHpp += $takeQty * $row->price;
                $remainingQty -= $takeQty;
            }

            // Rata-rata
            $historical = Procurement::where('product_id', $product->id)
                ->whereMonth('procurement_date', '!=', $now->month)
                ->get();

            $totalQty = $historical->sum('quantity');
            $totalCost = $historical->sum(function ($row) {
                return $row->quantity * $row->price;
            });

            $avgPrice = $totalQty > 0 ? $totalCost / $totalQty : 0;
            $avgHpp = $currentStock * $avgPrice;

            // Akumulasi
            $totalHppFifo += $fifoHpp;
            $totalHppAverage += $avgHpp;
            $totalStokNilai += $currentStock * $product->price;
        }

        return [
            Stat::make('Total HPP (FIFO)', 'Rp ' . number_format($totalHppFifo, 0, ',', '.'))
                ->description('Harga pokok berdasarkan metode FIFO')
                ->icon('heroicon-o-chart-bar')
                ->color('warning'),

            Stat::make('Total HPP (Rata-rata)', 'Rp ' . number_format($totalHppAverage, 0, ',', '.'))
                ->description('Harga pokok berdasarkan metode rata-rata')
                ->icon('heroicon-o-chart-pie')
                ->color('info'),

            Stat::make('Nilai Jual Stok', 'Rp ' . number_format($totalStokNilai, 0, ',', '.'))
                ->description('Total nilai penjualan stok saat ini')
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),
        ];
    }
}