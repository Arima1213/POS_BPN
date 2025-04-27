<?php

namespace App\Filament\Resources\ProductResource\Widgets;

use App\Models\Transactions;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
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

        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        $salesThisMonth = Transactions::where('created_at', '>=', $thisMonth)->sum('total');
        $salesLastMonth = Transactions::whereBetween('created_at', [$lastMonth, $thisMonth])->sum('total');

        $growth = $salesLastMonth > 0 ? (($salesThisMonth - $salesLastMonth) / $salesLastMonth) * 100 : 0;

        $today = Carbon::today();

        $salesToday = Transactions::whereDate('created_at', $today)->sum('total');

        return [
            Stat::make('Rata-rata Penjualan Harian', number_format($average, 0))
                ->description('Sejak awal bulan')
                ->icon('heroicon-o-calculator')
                ->color('info'),
            Stat::make('Pertumbuhan Pendapatan', number_format($growth, 2) . '%')
                ->description('vs Bulan Lalu')
                ->color($growth >= 0 ? 'success' : 'danger')
                ->icon('heroicon-o-trending-up'),
            Stat::make('Penjualan Hari Ini', number_format($salesToday, 0))
                ->description('Update harian')
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),
        ];
    }
}