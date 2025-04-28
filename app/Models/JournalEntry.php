<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $fillable = ['tanggal', 'kode', 'keterangan', 'kategori'];

    public function details()
    {
        return $this->hasMany(JournalEntryDetail::class);
    }
}