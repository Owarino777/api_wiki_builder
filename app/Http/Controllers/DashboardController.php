<?php

namespace App\Http\Controllers;

use App\Models\Build;
use App\Models\Game;
use App\Models\GameDraft;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        return view('dashboard', [
            'drafts' => GameDraft::query()
                ->where('owner_user_id', $user->id)
                ->latest('updated_at')
                ->take(8)
                ->get(),
            'games' => Game::query()
                ->where('owner_user_id', $user->id)
                ->latest('published_at')
                ->take(8)
                ->get(),
            'builds' => Build::query()
                ->where('user_id', $user->id)
                ->latest('updated_at')
                ->take(8)
                ->get(),
        ]);
    }
}
