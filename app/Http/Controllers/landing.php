<?php

namespace App\Http\Controllers;

use App\Models\ShowProductLanding;
use Illuminate\Http\Request;

class landing extends Controller
{
    public function index()
    {
        $landings = ShowProductLanding::where('status', 'aktif')->get();

        // Mapping untuk mengambil data berdasarkan tipe
        $landings = $landings->map(function ($landing) {
            if ($landing->tipe === 'produk') {
                $landing->load('product');
            } elseif ($landing->tipe === 'jasa') {
                $landing->load('service');
            }
            return $landing;
        });

        return view('home', compact('landings'));
    }
}
