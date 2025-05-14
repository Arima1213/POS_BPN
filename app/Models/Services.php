<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Services extends Model
{
    protected $fillable = [
        'name',
        'image',
        'price',
        'description',
        'unit_id',
    ];

    // Relasi ke satuan/unit
    public function unit()
    {
        return $this->belongsTo(Units::class);
    }

    // Relasi ke Transactions_Details
    public function transactionDetails()
    {
        return $this->hasMany(Transactions_Details::class, 'item_id')->where('item_type', self::class);
    }

    public function showProductLandings()
    {
        return $this->hasMany(ShowProductLanding::class, 'product_id');
    }
}