<?php

namespace App\Filament\Resources\ProductResource\Widgets;

use App\Models\Transactions;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget;
use Illuminate\Support\Carbon;

class RevenueGrowth extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        $salesThisMonth = Transactions::where('created_at', '>=', $thisMonth)->sum('total');
        $salesLastMonth = Transactions::whereBetween('created_at', [$lastMonth, $thisMonth])->sum('total');

        $growth = $salesLastMonth > 0 ? (($salesThisMonth - $salesLastMonth) / $salesLastMonth) * 100 : 0;

        return [
            Card::make('Pertumbuhan Pendapatan', number_format($growth, 2) . '%')
                ->description('vs Bulan Lalu')
                ->color($growth >= 0 ? 'success' : 'danger')
                ->icon('heroicon-o-trending-up'),
        ];
    }
}
