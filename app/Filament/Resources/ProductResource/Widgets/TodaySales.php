<?php

namespace App\Filament\Resources\ProductResource\Widgets;

use App\Models\Transactions;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget;
use Illuminate\Support\Carbon;

class TodaySales extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        $today = Carbon::today();

        $salesToday = Transactions::whereDate('created_at', $today)->sum('total');

        return [
            Card::make('Penjualan Hari Ini', number_format($salesToday, 0))
                ->description('Update harian')
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),
        ];
    }
}