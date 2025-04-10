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
}
