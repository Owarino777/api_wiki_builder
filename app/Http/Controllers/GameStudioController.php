<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use App\Models\Game;
use App\Models\GameDraft;
use App\Services\GameDrafts\GameCategoryTemplateRegistry;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class GameStudioController extends Controller
{
    public function __invoke(Request $request, GameCategoryTemplateRegistry $templateRegistry): View
    {
        $user = $request->user();

        $drafts = GameDraft::query()
            ->where('owner_user_id', $user->id)
            ->with('steps')
            ->latest('updated_at')
            ->get();

        $selectedDraft = null;
        $requestedDraftId = $request->integer('draft');
        if ($requestedDraftId > 0) {
            $selectedDraft = $drafts->firstWhere('id', $requestedDraftId);
        }

        if ($selectedDraft === null) {
            $selectedDraft = $drafts->first();
        }

        return view('studio.games.create', [
            'authUser' => $user,
            'drafts' => $drafts,
            'templates' => $templateRegistry->all(),
            'selectedDraftId' => $selectedDraft?->id,
            'publishedGames' => Game::query()
                ->where('owner_user_id', $user->id)
                ->latest('published_at')
                ->latest('updated_at')
                ->take(6)
                ->get(),
            'writeApiKey' => ApiKey::query()
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->orderBy('id')
                ->value('key'),
        ]);
    }
}
