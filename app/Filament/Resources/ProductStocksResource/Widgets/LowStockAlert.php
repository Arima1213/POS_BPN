<?php

namespace App\Filament\Resources\ProductStocksResource\Widgets;

use App\Models\Stock;
use Filament\Widgets\ChartWidget;

class LowStockAlert extends ChartWidget
{
    protected static string $view = 'filament.widgets.low-stock-alert';
    protected static ?int $sort = 2;

    protected function getType(): string
    {
        return 'bar'; // Replace 'bar' with the appropriate chart type if needed
    }

    public function getStocks()
    {
        return Stock::with('product')->whereColumn('quantity', '<=', 'minimum_stock')->get();
    }
}
