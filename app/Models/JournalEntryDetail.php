<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntryDetail extends Model
{
    protected $fillable = ['journal_entry_id', 'chart_of_account_id', 'tipe', 'jumlah', 'deskripsi'];

    public function jurnal()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    public function akun()
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }
}
