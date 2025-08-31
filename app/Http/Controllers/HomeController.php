<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\News;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredGames = Game::where('is_featured', true)
                            ->whereNull('deleted_at')
                            ->limit(4)
                            ->get();

        $featuredNews = News::where('is_featured', true)
                           ->orderBy('published_at', 'desc')
                           ->limit(5)
                           ->get();

        $latestNews = News::orderBy('published_at', 'desc')
                         ->limit(3)
                         ->get();

        return view('welcome', [
            'featuredGames' => $featuredGames,
            'featuredNews' => $featuredNews,
            'news' => $latestNews
        ]);
    }
}