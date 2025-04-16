<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chart_Of_Account extends Model
{

    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'kelompok',
        'tipe',
        'jenis_beban',
        'deskripsi',
    ];
}