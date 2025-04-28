<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Procurement extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'quantity', 'price', 'procurement_date', 'supplier_id', 'note'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class, 'product_id', 'product_id');
    }
}
