<?php

namespace App\Http\Controllers;

use App\Models\ArtistProfile;

class HomeController extends Controller
{
    public function index()
    {
        $featuredArtists = ArtistProfile::with('user')
            ->where('is_active', true)
            ->inRandomOrder()
            ->limit(6)
            ->get();

        return view('home.index', compact('featuredArtists'));
    }
}
