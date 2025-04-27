<?php

namespace App\Filament\Resources\ProductStocksResource\Widgets;

use App\Models\Stock;
use Filament\Widgets\ChartWidget;

class LowStockAlert extends ChartWidget
{
    protected static string $view = 'low-stock-alert';
    protected static ?int $sort = 2;

    protected function getType(): string
    {
        return 'bar';
    }

    public function getStocks()
    {
        return Stock::with('product')->whereColumn('quantity', '<=', 'minimum_stock')->get();
    }
}