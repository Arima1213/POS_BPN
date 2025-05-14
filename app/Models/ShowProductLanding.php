<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShowProductLanding extends Model
{
    protected $table = 'show_product_landings';

    protected $fillable = [
        'tipe',
        'status',
        'product_id',
    ];

    /**
     * Relasi ke model Product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Relasi ke model Service (jika tipe = 'jasa').
     */
    public function service()
    {
        return $this->belongsTo(Services::class, 'product_id');
    }

    public function getItemNameAttribute()
    {
        return $this->tipe === 'produk'
            ? $this->product?->name
            : $this->service?->name;
    }

    public function getItemPriceAttribute()
    {
        return $this->tipe === 'produk'
            ? $this->product?->price
            : $this->service?->price;
    }
}
