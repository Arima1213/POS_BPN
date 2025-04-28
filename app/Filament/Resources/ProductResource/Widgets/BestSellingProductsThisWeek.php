<?php

namespace App\Filament\Resources\ProductResource\Widgets;

use App\Models\Product;
use App\Models\Transactions_Details;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class BestSellingProductsThisWeek extends Widget
{
    protected static string $view = 'best-selling-products-this-week'; // <<< diperbaiki
    public function getBestSellingProductsThisWeek()
    {
        $oneWeekAgo = now()->subWeek();

        $products = Transactions_Details::where('item_type', Product::class) // hanya product
            ->whereHas('transaction', function ($query) use ($oneWeekAgo) {
                $query->where('created_at', '>=', $oneWeekAgo);
            })
            ->select('item_id', 'item_type', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('item_id', 'item_type')
            ->with('item.category')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        return $products ?? collect();
    }
}
