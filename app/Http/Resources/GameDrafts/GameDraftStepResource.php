<?php

namespace App\Http\Resources\GameDrafts;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameDraftStepResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $snapshot = is_array($this->payload_snapshot) ? $this->payload_snapshot : [];

        return [
            'step_key' => $this->step_key,
            'status' => $this->status,
            'blocking_errors_count' => $this->blocking_errors_count,
            'warnings_count' => $this->warnings_count,
            'last_saved_at' => $this->last_saved_at,
            'last_validated_at' => $this->last_validated_at,
            'errors' => array_values(array_filter($snapshot['errors'] ?? [], 'is_string')),
            'warnings' => array_values(array_filter($snapshot['warnings'] ?? [], 'is_string')),
            'payload_snapshot' => collect($snapshot)
                ->except(['errors', 'warnings'])
                ->all(),
        ];
    }
}
