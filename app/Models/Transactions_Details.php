<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transactions_Details extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'item_type',
        'item_id',
        'price',
        'quantity',
        'subtotal',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transactions::class);
    }

    public function item()
    {
        return $this->morphTo(__FUNCTION__, 'item_type', 'item_id');
    }

    // Atau alternatif manual jika tidak menggunakan morph
    public function product()
    {
        return $this->belongsTo(Product::class, 'item_id')->where('item_type', 'product');
    }

    public function service()
    {
        return $this->belongsTo(Services::class, 'item_id')->where('item_type', 'service');
    }
}