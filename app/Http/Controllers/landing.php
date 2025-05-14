<?php

namespace App\Http\Controllers;

use App\Models\OurTeam;
use App\Models\ShowProductLanding;
use Illuminate\Http\Request;

class landing extends Controller
{
    public function index()
    {
        $landings = ShowProductLanding::where('status', 'aktif')
            ->with(['product', 'service']) // eager load dua-duanya
            ->get();

        $teams = OurTeam::all();

        return view('home', compact('landings', 'teams'));
    }
}
