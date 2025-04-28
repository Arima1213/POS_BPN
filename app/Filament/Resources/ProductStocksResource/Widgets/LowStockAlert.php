<?php

namespace App\Filament\Resources\ProductStocksResource\Widgets;

use App\Models\ProductStock;
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
        return ProductStock::with('product')->whereColumn('current_stock', '<=', 'minimum_stock')->get();
    }
}