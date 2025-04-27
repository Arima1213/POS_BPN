<?php

namespace App\Filament\Resources\ProductResource\Widgets;

use App\Models\Transactions_Details;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BestSellingProductsThisWeek extends Widget
{
    protected static string $view = 'best-selling-products-this-week'; // <<< diperbaiki
    public function getBestSellingProductsThisWeek()
    {
        $oneWeekAgo = Carbon::now()->subWeek();
        return Transactions_Details::where('item_type', 'App\Models\Product')
            ->whereHas('transaction', function ($query) use ($oneWeekAgo) {
                $query->where('created_at', '>=', $oneWeekAgo);
            })
            ->select('item_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('item_id')
            ->with('product.category') // <<< eager load category sekalian
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();
    }
}