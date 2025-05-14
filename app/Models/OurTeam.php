<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OurTeam extends Model
{
    protected $table = 'our_teams';

    protected $fillable = [
        'nama',
        'deskripsi',
        'facebook_url',
        'instagram_url',
        'whatsapp_url',
    ];
}