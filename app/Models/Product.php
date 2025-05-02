<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'itemcode',
        'image',
        'name',
        'brand',
        'itemweight',
        'description',
        'price'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockHistories()
    {
        return $this->hasMany(StockHistory::class);
    }

    // Relasi ke Transactions_Details
    public function transactionDetails()
    {
        return $this->hasMany(Transactions_Details::class, 'item_id')->where('item_type', self::class);
    }
}
