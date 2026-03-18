<?php

namespace App\Http\Resources\GameDrafts;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameDraftArchetypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'game_draft_id' => $this->game_draft_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
