<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;

class ApiHomeController extends Controller
{
    public function __invoke()
    {
        return ApiResponse::success([
            'name' => 'Wiki Builder API',
            'version' => 'v1',
            'ui_url' => url('/'),
            'api_root' => url('/api/v1'),
            'healthcheck' => url('/up'),
            'public_routes' => [
                ['method' => 'GET', 'path' => '/api/v1/games', 'usage' => 'Liste des jeux'],
                ['method' => 'GET', 'path' => '/api/v1/games/{id}/items', 'usage' => 'Catalogue d un jeu'],
                ['method' => 'GET', 'path' => '/api/v1/items/{id}', 'usage' => 'Detail complet d un item'],
                ['method' => 'GET', 'path' => '/api/v1/builds', 'usage' => 'Builds publics ou filtres'],
                ['method' => 'POST', 'path' => '/api/v1/builds/compare', 'usage' => 'Comparaison de deux builds'],
            ],
            'protected_routes' => [
                'write_header' => 'API-KEY',
                'admin_header' => 'X-USER-ID',
                'write_examples' => [
                    ['method' => 'POST', 'path' => '/api/v1/builds'],
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
        ], 'Wiki Builder API root');
    }
}
