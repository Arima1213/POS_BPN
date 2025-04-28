<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChartOfAccount extends Model
{
    use HasFactory;

    protected $table = 'chart_of_accounts';

    protected $fillable = [
        'kode',
        'nama',
        'kelompok',
        'tipe',
        'jenis_beban',
        'deskripsi',
    ];

    public function journalEntryDetails()
    {
        return $this->hasMany(JournalEntryDetail::class, 'chart_of_account_id');
    }
}