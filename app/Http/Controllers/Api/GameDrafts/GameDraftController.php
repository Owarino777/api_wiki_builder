<?php

namespace App\Http\Controllers\Api\GameDrafts;

use App\Enums\UserRole;
use App\Http\Requests\GameDrafts\StoreGameDraftRequest;
use App\Http\Requests\GameDrafts\UpdateGameDraftFoundationRequest;
use App\Http\Requests\GameDrafts\UpdateGameDraftStructureRequest;
use App\Http\Resources\GameDrafts\GameDraftResource;
use App\Http\Resources\GameResource;
use App\Models\GameDraft;
use App\Support\ApiResponse;
use App\Services\GameDrafts\GameCategoryTemplateRegistry;
use App\Services\GameDrafts\GameDraftProgressService;
use App\Services\GameDrafts\GameDraftPublicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GameDraftController extends OwnedGameDraftController
{
    public function __construct(
        private readonly GameCategoryTemplateRegistry $templateRegistry,
        private readonly GameDraftProgressService $progressService,
        private readonly GameDraftPublicationService $publicationService
    ) {
    }

    public function index(Request $request)
    {
        $user = $this->requestUser($request);
        if ($user === null) {
            return ApiResponse::error('Authenticated user context is required.', 401);
        }

        $query = GameDraft::query()
            ->with(['steps'])
            ->orderByDesc('updated_at');

        if ($user->role !== UserRole::Admin || ! $request->boolean('all')) {
            $query->where('owner_user_id', $user->id);
        }

        $perPage = max(1, min((int) $request->query('per_page', 15), 100));
        $drafts = $query->paginate($perPage);

        return ApiResponse::success(GameDraftResource::collection($drafts)->response()->getData(true));
    }

    public function store(StoreGameDraftRequest $request)
    {
        $user = $this->requestUser($request);
        if ($user === null) {
            return ApiResponse::error('Authenticated user context is required.', 401);
        }

        $data = $request->validated();
        $template = $this->templateRegistry->find($data['category_key']);

        if ($template === null) {
            return ApiResponse::error('Unknown game category template.', 422);
        }

        $slug = $data['slug'] ?? Str::slug($data['name']);
        if ($this->ownerSlugExists($user->id, $slug)) {
            return ApiResponse::error('A draft with this slug already exists for this user.', 422);
        }

        $draft = GameDraft::query()->create([
            'owner_user_id' => $user->id,
            'category_key' => $template['key'],
            'template_version' => $template['version'],
            'status' => 'draft',
            'current_step' => 'foundation',
            'name' => $data['name'],
            'slug' => $slug,
            'visibility' => 'private',
            'settings' => $this->templateRegistry->defaults($template['key']),
            'meta' => [
                'template_label' => $template['label'],
            ],
        ]);

        $draft = $this->progressService->refresh($draft, 'foundation');

        return ApiResponse::success(
            (new GameDraftResource($this->loadDraft($draft->id)))->toArray($request),
            'Draft created.',
            201
        );
    }

    public function show(Request $request, int $id)
    {
        $draft = $this->resolveDraft($request, $id);
        if ($draft === null) {
            return ApiResponse::error('Draft not found.', 404);
        }

        $draft = $this->progressService->refresh($draft);

        return ApiResponse::success((new GameDraftResource($this->loadDraft($draft->id)))->toArray($request));
    }

    public function destroy(Request $request, int $id)
    {
        $draft = $this->resolveDraft($request, $id);
        if ($draft === null) {
            return ApiResponse::error('Draft not found.', 404);
        }

        if ($draft->published_game_id !== null) {
            return ApiResponse::error('Published drafts cannot be deleted.', 409);
        }

        $draft->delete();

        return ApiResponse::success(null, 'Draft deleted.');
    }

    public function foundation(UpdateGameDraftFoundationRequest $request, int $id)
    {
        $draft = $this->resolveDraft($request, $id);
        if ($draft === null) {
            return ApiResponse::error('Draft not found.', 404);
        }

        $data = $request->validated();
        $slug = $data['slug'] ?? $draft->slug;

        if ($this->ownerSlugExists($draft->owner_user_id, $slug, $draft->id)) {
            return ApiResponse::error('A draft with this slug already exists for this user.', 422);
        }

        $draft->fill([
            'name' => $data['name'] ?? $draft->name,
            'slug' => $slug,
            'description' => array_key_exists('description', $data) ? $data['description'] : $draft->description,
            'visibility' => $data['visibility'] ?? $draft->visibility,
        ])->save();

        $draft = $this->progressService->refresh($draft, 'foundation');

        return ApiResponse::success((new GameDraftResource($this->loadDraft($draft->id)))->toArray($request), 'Foundation updated.');
    }

    public function structure(UpdateGameDraftStructureRequest $request, int $id)
    {
        $draft = $this->resolveDraft($request, $id);
        if ($draft === null) {
            return ApiResponse::error('Draft not found.', 404);
        }

        if (($draft->items()->exists() || $draft->modifiers()->exists())
            && ($request->has('allowed_item_slots') || $request->has('allowed_modifier_types') || $request->has('supports_modifiers'))) {
            return ApiResponse::error('Structure cannot be reshaped once items or modifiers already exist in the draft.', 409);
        }
        if ($draft->archetypes()->exists() && $request->boolean('supports_archetypes') === false) {
            return ApiResponse::error('Archetypes cannot be disabled while the draft already contains archetypes.', 409);
        }

        $settings = is_array($draft->settings) ? $draft->settings : [];

        foreach ($request->validated() as $key => $value) {
            $settings[$key] = $value;
        }

        if (($settings['supports_modifiers'] ?? true) === false) {
            $settings['allowed_modifier_types'] = [];
        }

        $draft->forceFill([
            'settings' => $settings,
        ])->save();

        $draft = $this->progressService->refresh($draft, 'structure');

        return ApiResponse::success((new GameDraftResource($this->loadDraft($draft->id)))->toArray($request), 'Structure updated.');
    }

    public function publish(Request $request, int $id)
    {
        $draft = $this->resolveDraft($request, $id);
        if ($draft === null) {
            return ApiResponse::error('Draft not found.', 404);
        }

        try {
            $game = $this->publicationService->publish($draft);
        } catch (\RuntimeException $exception) {
            return ApiResponse::error('Draft publication blocked.', 422, [
                'reason' => $exception->getMessage(),
            ]);
        }

        $draft = $this->progressService->refresh($draft->fresh(), 'review');

        return ApiResponse::success([
            'draft' => (new GameDraftResource($this->loadDraft($draft->id)))->toArray($request),
            'game' => (new GameResource($game))->toArray($request),
        ], 'Draft published.', 201);
    }

    private function ownerSlugExists(int $ownerUserId, string $slug, ?int $exceptDraftId = null): bool
    {
        return GameDraft::query()
            ->where('owner_user_id', $ownerUserId)
            ->where('slug', $slug)
            ->when($exceptDraftId !== null, fn ($query) => $query->whereKeyNot($exceptDraftId))
            ->exists();
    }

    private function loadDraft(int $draftId): GameDraft
    {
        return GameDraft::query()
            ->with([
                'steps',
                'archetypes',
                'categories',
                'items.categories',
                'items.itemSlots',
                'items.modifiers',
                'modifiers.items',
            ])
            ->findOrFail($draftId);
    }
}
