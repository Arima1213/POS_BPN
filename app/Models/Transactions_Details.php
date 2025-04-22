<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transactions_Details extends Model
{
    use HasFactory;

    protected $table = 'transactions_details';

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

    public function product()
    {
        return $this->belongsTo(Product::class, 'item_id');
    }

    public function service()
    {
        return $this->belongsTo(Services::class, 'item_id');
    }
}
