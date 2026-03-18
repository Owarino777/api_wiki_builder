<?php

namespace App\Http\Resources\GameDrafts;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameDraftResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'owner_user_id' => $this->owner_user_id,
            'published_game_id' => $this->published_game_id,
            'category_key' => $this->category_key,
            'template_version' => $this->template_version,
            'status' => $this->status,
            'current_step' => $this->current_step,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'visibility' => $this->visibility,
            'completion_percent' => $this->completion_percent,
            'settings' => $this->settings,
            'meta' => $this->meta,
            'last_validated_at' => $this->last_validated_at,
            'published_at' => $this->published_at,
            'steps' => $this->whenLoaded('steps', fn () => GameDraftStepResource::collection($this->steps->sortBy('step_key')->values())),
            'archetypes' => $this->whenLoaded('archetypes', fn () => GameDraftArchetypeResource::collection($this->archetypes)),
            'categories' => $this->whenLoaded('categories', fn () => GameDraftCategoryResource::collection($this->categories)),
            'items' => $this->whenLoaded('items', fn () => GameDraftItemResource::collection($this->items)),
            'modifiers' => $this->whenLoaded('modifiers', fn () => GameDraftModifierResource::collection($this->modifiers)),
            'counts' => [
                'steps' => $this->relationLoaded('steps') ? $this->steps->count() : null,
                'archetypes' => $this->relationLoaded('archetypes') ? $this->archetypes->count() : null,
                'categories' => $this->relationLoaded('categories') ? $this->categories->count() : null,
                'items' => $this->relationLoaded('items') ? $this->items->count() : null,
                'modifiers' => $this->relationLoaded('modifiers') ? $this->modifiers->count() : null,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
