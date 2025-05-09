<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductStock extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'current_stock', 'minimum_stock'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stockHistories()
    {
        return $this->hasMany(StockHistory::class, 'product_id', 'product_id');
    }
}