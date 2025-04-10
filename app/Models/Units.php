<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Units extends Model
{
    protected $fillable = [
        'name',
        'short',
    ];

    // Relasi ke layanan/jasa
    public function services()
    {
        return $this->hasMany(Services::class);
    }
}
