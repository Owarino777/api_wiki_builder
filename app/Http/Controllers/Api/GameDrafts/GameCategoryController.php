<?php

namespace App\Http\Controllers\Api\GameDrafts;

use App\Support\ApiResponse;
use App\Services\GameDrafts\GameCategoryTemplateRegistry;

class GameCategoryController extends OwnedGameDraftController
{
    public function __construct(
        private readonly GameCategoryTemplateRegistry $templateRegistry
    ) {
    }

    public function index()
    {
        return ApiResponse::success($this->templateRegistry->all());
    }

    public function show(string $key)
    {
        $template = $this->templateRegistry->find($key);

        if ($template === null) {
            return ApiResponse::error('Game category template not found.', 404);
        }

        return ApiResponse::success($template);
    }
}
