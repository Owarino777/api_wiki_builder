<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class ApiHomeController extends Controller
{
    public function __invoke(Request $request)
    {
        $payload = [
            'name' => 'Wiki Builder API',
            'version' => 'v1',
            'ui_url' => url('/'),
            'api_root' => url('/api/v1'),
            'healthcheck' => url('/up'),
            'auth_ui' => [
                'login' => url('/login'),
                'register' => url('/register'),
                'dashboard' => url('/dashboard'),
            ],
            'public_routes' => [
                ['method' => 'GET', 'path' => '/api/v1/games', 'usage' => 'Liste des jeux'],
                ['method' => 'GET', 'path' => '/api/v1/games/{id}/items', 'usage' => 'Catalogue d un jeu'],
                ['method' => 'GET', 'path' => '/api/v1/items/{id}', 'usage' => 'Detail complet d un item'],
                ['method' => 'GET', 'path' => '/api/v1/builds', 'usage' => 'Builds publics ou filtres'],
                ['method' => 'POST', 'path' => '/api/v1/builds/compare', 'usage' => 'Comparaison de deux builds'],
                ['method' => 'GET', 'path' => '/api/v1/game-categories', 'usage' => 'Templates backend pour creer un jeu utilisateur'],
            ],
            'protected_routes' => [
                'write_header' => 'API-KEY',
                'actor_headers' => ['X-WIKI-ACTOR-ID', 'X-BUILDER-USER-ID', 'X-USER-ID'],
                'user_context' => 'Le user d ecriture est resolu via un header actor dedie ou futur auth contextualise, jamais via user_id payload.',
                'write_examples' => [
                    ['method' => 'POST', 'path' => '/api/v1/builds'],
                    ['method' => 'POST', 'path' => '/api/v1/game-drafts'],
                    ['method' => 'POST', 'path' => '/api/v1/game-drafts/{id}/publish'],
                    ['method' => 'POST', 'path' => '/api/v1/items'],
                    ['method' => 'POST', 'path' => '/api/v1/modifiers'],
                ],
            ],
            'examples' => [
                [
                    'label' => 'Lister les jeux',
                    'method' => 'GET',
                    'path' => '/api/v1/games?per_page=20',
                ],
                [
                    'label' => 'Charger le catalogue d un jeu',
                    'method' => 'GET',
                    'path' => '/api/v1/games/1/items?per_page=100&sort_by=score&sort_dir=desc',
                ],
                [
                    'label' => 'Comparer deux builds',
                    'method' => 'POST',
                    'path' => '/api/v1/builds/compare',
                    'body' => [
                        'left_build_id' => 1,
                        'right_build_id' => 2,
                    ],
                ],
            ],
        ];

        return ApiResponse::success($payload, 'Wiki Builder API root');
    }
}
