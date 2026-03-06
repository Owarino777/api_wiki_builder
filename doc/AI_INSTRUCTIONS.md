# AI_INSTRUCTIONS.md - Wiki Builder API

ROLE:
- Senior Laravel backend engineer.

TARGET:
- Laravel 12
- PHP 8.3+
- MySQL 8
- Docker Compose

REFERENCE:
- `doc/architecture_modifications.md`

## Non-negotiable schema

The schema uses:
- `games`
- `users`
- `api_keys`
- `archetypes`
- `items`
- `item_slots`
- `modifiers`
- `categories`
- `builds`
- `item_categories`
- `item_modifiers`
- `build_items`
- `build_slots`

## Non-negotiable business rules

1. A build is single-game.
2. An archetype must belong to the same game as the build.
3. An item must belong to the same game as the build.
4. A modifier must belong to the same game as the build.
5. A modifier can be equipped only if compatibility exists in `item_modifiers`.
6. A modifier must be equipped through a valid `item_slot_id`.
7. The slot must belong to the targeted item.
8. The slot must accept the modifier type unless it is wildcard.
9. Build comparison is allowed only for builds from the same game.
10. Summary totals are item stats + modifier deltas with NULL treated as 0.

## API naming target

Public:
- `/games`
- `/games/{id}/archetypes`
- `/games/{id}/items`
- `/games/{id}/modifiers`
- `/categories`
- `/items`
- `/items/{id}`
- `/modifiers`
- `/modifiers/{id}`
- `/builds`
- `/builds/{id}`
- `/builds/{id}/summary`
- `/builds/share/{shareToken}`
- `/builds/compare`

Write:
- `/archetypes`
- `/categories`
- `/items`
- `/modifiers`
- `/items/{itemId}/modifiers/{modifierId}`
- `/items/{itemId}/categories/{categoryId}`
- `/builds`

## Builder payload target

```json
{
  "game_id": 1,
  "archetype_id": 1,
  "title": "My Build",
  "items": [
    {
      "item_id": 12,
      "position": 1
    }
  ],
  "slots": [
    {
      "item_id": 12,
      "item_slot_id": 42,
      "modifier_id": 8
    }
  ]
}
```

## Design goal

The API must support adding new games and their data without rewriting code for slot logic or modifier logic.
