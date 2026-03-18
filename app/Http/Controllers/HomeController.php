<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke(Request $request): View
    {
        return view('welcome', [
            'authUser' => $request->user(),
            'publicGames' => Game::query()
                ->where('visibility', 'public')
                ->withCount(['archetypes', 'items', 'modifiers'])
                ->orderByDesc('published_at')
                ->orderBy('name')
                ->take(6)
                ->get(),
        ]);
    }
}
