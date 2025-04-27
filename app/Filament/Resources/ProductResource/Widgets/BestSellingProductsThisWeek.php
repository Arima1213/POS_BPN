<?php

namespace App\Filament\Resources\ProductResource\Widgets;

use App\Models\Transactions_Details;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BestSellingProductsThisWeek extends Widget
{
    protected static string $view = 'best-selling-products-this-week';

    protected function getData(): array
    {
        $oneWeekAgo = Carbon::now()->subWeek();

        $products = Transactions_Details::where('item_type', 'App\Models\Product')
            ->whereHas('transaction', function ($query) use ($oneWeekAgo) {
                $query->where('created_at', '>=', $oneWeekAgo);
            })
            ->select('item_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('item_id')
            ->with('product')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        return [
            'products' => $products,
        ];
    }
}
