<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expanse extends Model
{
    protected $table = 'expenses';
    protected $fillable = [
        'tanggal',
        'deskripsi',
        'akun_beban_id',
        'jumlah',
    ];

    public function akunBeban()
    {
        return $this->belongsTo(ChartOfAccount::class, 'akun_beban_id');
    }
}