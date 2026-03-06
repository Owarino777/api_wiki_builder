<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BuildProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'game_id' => $this->game_id,
            'name' => $this->name,
            'description' => $this->description,
            'resource_type' => 'archetype',
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
