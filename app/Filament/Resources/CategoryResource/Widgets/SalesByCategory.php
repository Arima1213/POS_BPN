<?php

namespace App\Filament\Resources\CategoryResource\Widgets;

use App\Models\Category;
use Filament\Widgets\ChartWidget;

class SalesByCategory extends ChartWidget
{
    protected static ?string $heading = 'Penjualan Produk per Kategori';
    protected static ?string $maxHeight = '300px';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $categories = Category::with(['products.transactionDetails'])->get();

        $labels = [];
        $data = [];

        foreach ($categories as $category) {
            $labels[] = $category->name;

            $total = 0;
            foreach ($category->products as $product) {
                $total += $product->transactionDetails->sum('subtotal');
            }

            $data[] = $total;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Total Penjualan (Rp)',
                    'data' => $data,
                    'backgroundColor' => '#4f46e5',
                ],
            ],
        ];
    }
}
