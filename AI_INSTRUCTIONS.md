# AI_INSTRUCTIONS.md - Wiki Builder (Laravel API)

ROLE: Senior Laravel backend engineer.

TARGET STACK:
- Laravel 12
- PHP 8.3+
- MySQL 8.0
- Docker Compose (app + db)

Target stack: Laravel 12, PHP 8.3+, MySQL 8, Docker Compose.

INPUTS (provided):
- MPD (DBML) + SQL schema
- MCD + functional spec (PDF/text)
- AI_INSTRUCTIONS.md (this file)

NON-NEGOTIABLE:
- Implement database schema exactly as MPD, including:
  - `build_equipment_modifiers` composite PK: (`build_id`, `equipment_item_id`, `equipment_modifier_id`)
  - `equipment_item_id` is NOT NULL
- Enforce business rules everywhere:
  1) build is single-game (all referenced rows share `build.game_id`)
  2) modifier can be attached only if compatibility exists
  3) summary totals = SUM(item stats) + SUM(modifier deltas), NULL treated as 0
- Security:
  - Write routes require `API-KEY` header validated against `api_keys` (`is_active=1`)
  - Admin routes require `CheckRole` middleware using `X-USER-ID` -> `users.role`
  - Throttle 60/min on all routes
  - Laravel 12 middleware registration:
    - Register middleware aliases in `bootstrap/app.php` (not `app/Http/Kernel.php`).
    - Use route groups to apply `throttle:60,1`, `check.api.key`, and `check.role`.
- Do NOT use Laravel default migrations (`users/cache/jobs`).
- Replace default migrations with MPD migrations only.
- The `users` table must match MPD fields (including `role`, `username`, `email`, etc.).
- Ensure `api_keys` and all pivot tables are present per MPD.
- Ensure a clean database reset workflow.

LARAVEL 12 REQUIREMENT:
- Register middleware aliases in `bootstrap/app.php` (not `Kernel.php`).
- Use route groups with middleware aliases and throttle.

## Goal

Generate a Laravel REST API for the Wiki Builder project using the provided MPD/DBML and business rules.

---

## Non-negotiable business rules

1. **Single-game build rule**  
   A build can only reference `build_profile`, `equipment_items` and `equipment_modifiers` with the same `game_id` as the build.

2. **Compatibility rule**  
   A modifier can be added to an item only if (`equipment_item_id`, `equipment_modifier_id`) exists in `equipment_item_compatible_modifiers`.

3. **Summary formula (NULL = 0)**  
   Build summary must compute totals treating NULL as 0:
   - `total_score = SUM(equipment_items.score) + SUM(equipment_modifiers.delta_score)`
   - Same logic for `attack / defense / utility / accuracy / mobility` using `delta_*`.

4. **Security (course-oriented)**
   - All write routes require header `API-KEY` validated against `api_keys.key` (and `is_active = true`) in DB.
   - RBAC minimal: admin-only routes require a user with `users.role == "admin"`.
   - **User identification is simplified**: requests include header `X-USER-ID`. The `CheckRole` middleware loads the user from DB using `X-USER-ID` and checks `role`.
   - Throttle: 60 requests/minute on all routes.
   - Laravel 12 middleware registration:
     - Register middleware aliases in `bootstrap/app.php` (not `app/Http/Kernel.php`).
     - Use route groups to apply `throttle:60,1`, `check.api.key`, and `check.role`.

5. **MPD-first schema & migrations**
   - Ignore/remove Laravel default migrations (`create_users_table`, `create_cache_table`, `create_jobs_table`).
   - Generate only MPD-driven migrations.
   - `users` must match MPD (`username`, `email`, `role`, timestamps, etc.).
   - Include `api_keys`, pivots, and composite PK on `build_equipment_modifiers` (`build_id`, `equipment_item_id`, `equipment_modifier_id`).
   - Provide a clean reset workflow (`migrate:fresh --seed --force`).

---

## API endpoints (minimum)

### Public (read-only)

```txt
GET  /games
GET  /games/{id}
GET  /games/{id}/build-profiles
GET  /games/{id}/equipment-items
GET  /games/{id}/equipment-modifiers
GET  /tags
GET  /equipment-items/{id}
GET  /equipment-modifiers/{id}
GET  /builds/{id}
```

### Write (protected by `API-KEY`)

```txt
POST   /builds
PUT    /builds/{id}
DELETE /builds/{id}

POST   /build-profiles
PUT    /build-profiles/{id}
DELETE /build-profiles/{id}

POST   /equipment-items
PUT    /equipment-items/{id}
DELETE /equipment-items/{id}

POST   /equipment-modifiers
PUT    /equipment-modifiers/{id}
DELETE /equipment-modifiers/{id}

POST   /equipment-items/{equipment_item_id}/compatible-modifiers/{equipment_modifier_id}
DELETE /equipment-items/{equipment_item_id}/compatible-modifiers/{equipment_modifier_id}

POST   /equipment-items/{equipment_item_id}/tags/{tag_id}
DELETE /equipment-items/{equipment_item_id}/tags/{tag_id}
```

### Builder calculations

```txt
GET  /builds/{id}/summary
POST /builds/compare
```

---

## Request / Response contracts

### `GET /builds/{id}/summary`

```json
{
  "build_id": 1,
  "items_count": 0,
  "modifiers_count": 0,
  "totals": {
    "score": 0,
    "attack": 0,
    "defense": 0,
    "utility": 0,
    "accuracy": 0,
    "mobility": 0
  }
}
```

### `POST /builds/compare`

Request body:

```json
{
  "left_build_id": 1,
  "right_build_id": 2
}
```

Response body:

```json
{
  "left": {
    "build_id": 1,
    "totals": {
      "score": 0,
      "attack": 0,
      "defense": 0,
      "utility": 0,
      "accuracy": 0,
      "mobility": 0
    }
  },
  "right": {
    "build_id": 2,
    "totals": {
      "score": 0,
      "attack": 0,
      "defense": 0,
      "utility": 0,
      "accuracy": 0,
      "mobility": 0
    }
  },
  "diff": {
    "score": 0,
    "attack": 0,
    "defense": 0,
    "utility": 0,
    "accuracy": 0,
    "mobility": 0
  }
}
```

---

## What to generate (order matters)

### Phase 1 - Database & boot

- Laravel migrations for all tables in the MPD (including pivots, composite PKs, indexes, foreign keys).
- Remove/ignore Laravel default migrations (`users/cache/jobs`) and keep MPD migrations only.
- Seeders:
  - 2 games
  - 5 build_profiles
  - 20 equipment_items
  - 20 equipment_modifiers
  - compatibility rows
  - tags + item tags
  - 1 admin user (`role=admin`) + 1 standard user (`role=user`)
  - 1 `api_key` for Postman testing (`is_active=true`)
- `docker-compose.yml` (app + db) + `.env.example`

### Phase 2 - Models (Eloquent)

Generate models with relationships:

- `Game` -> `hasMany` BuildProfile, EquipmentItem, EquipmentModifier, Build
- `EquipmentItem` -> `belongsTo` Game
- `EquipmentItem` -> `belongsToMany` Tag (pivot: `equipment_item_tags`)
- `EquipmentItem` -> `belongsToMany` EquipmentModifier (pivot: `equipment_item_compatible_modifiers`)
- `Build` -> `belongsTo` Game; `belongsTo` optional BuildProfile; `belongsTo` optional User
- `Build` -> `belongsToMany` EquipmentItem (pivot: `build_equipment_items`, with `position`)
- `Build` -> `belongsToMany` EquipmentModifier (pivot: `build_equipment_modifiers`, with required `equipment_item_id`)

### Phase 3 - API structure

- Controllers (CRUD) for: `games`, `build_profiles`, `equipment_items`, `equipment_modifiers`, `tags`, `builds`
- Filtering / sorting / pagination on `equipment_items` and `equipment_modifiers` by `game_id` and fields: `slot`, `rarity`, `score` range, `tag`, `modifier_type`

### Phase 4 - Validation & responses

- Form Requests for all create/update endpoints and for the compare endpoint.
- Enforce business rules in requests or service layer.
- Use API Resources for consistent JSON output.
- Provide `ApiResponse` helper (success/error) and consistent validation error format.

### Phase 5 - Middlewares

- `CheckApiKey`: reads `API-KEY` header, validates in DB, returns `401` if invalid or inactive.
- `CheckRole`: reads `X-USER-ID` header, loads user from DB, checks `role`, returns `403` if forbidden.
- Register middleware aliases in `bootstrap/app.php` (Laravel 12).
- Use route groups to apply `throttle:60,1`, `check.api.key`, and `check.role`.

---

## API conventions

- Prefix all routes with `/api/v1`.
- Implement endpoints and JSON contracts exactly as this file.
- Responses must be uniform via `ApiResponse::success()` / `ApiResponse::error()`.

---

## Output rules

- Generate complete files (no snippets).
- Keep naming consistent with the MPD.
- English comments only.
- Provide a `README.md` with exact commands:

```bash
docker compose up -d --build
php artisan migrate --force
php artisan db:seed --force
php artisan migrate:fresh --seed --force
```

- Include example `curl` calls using `API-KEY` and `X-USER-ID` headers.
