<?php

namespace App\Filament\Resources\CategoryResource\Widgets;

use App\Models\Category;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class SalesByCategory extends ChartWidget
{
    protected static ?string $heading = 'Penjualan Berdasarkan Kategori';
    protected static string $color = 'primary';

    protected function getData(): array
    {
        $categories = Category::withCount(['products as sales' => function ($query) {
            $query->join('transactions_details', 'products.id', '=', 'transactions_details.item_id')
                ->where('transactions_details.item_type', 'App\Models\Product')
                ->select(DB::raw('SUM(transactions_details.quantity)'));
        }])->get();

        return [
            'datasets' => [
                [
                    'label' => 'Penjualan',
                    'data' => $categories->pluck('sales'),
                ],
            ],
            'labels' => $categories->pluck('name'),
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // atau 'pie', 'doughnut', bebas
    }
}