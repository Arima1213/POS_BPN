<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OwnerTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'tipe', // setor_modal atau prive
        'jumlah',
        'keterangan',
    ];
}