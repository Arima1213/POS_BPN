<?php

namespace App\Filament\Resources\ProductResource\Widgets;

use App\Models\Transactions;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget;
use Illuminate\Support\Carbon;

class AverageDailySales extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $today = Carbon::now();

        $totalSales = Transactions::whereBetween('created_at', [$startOfMonth, $today])->sum('total');
        $daysPassed = $today->diffInDays($startOfMonth) ?: 1; // jangan sampai 0

        $average = $totalSales / $daysPassed;

        return [
            Card::make('Rata-rata Penjualan Harian', number_format($average, 0))
                ->description('Sejak awal bulan')
                ->icon('heroicon-o-calculator')
                ->color('info'),
        ];
    }
}