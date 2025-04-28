<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'quantity', 'minimum_stock'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function procurements()
    {
        return $this->hasMany(Procurement::class);
    }

    // Ini relasi history
    public function histories()
    {
        return $this->hasMany(StockHistory::class, 'product_id', 'product_id');
    }
}
