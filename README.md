# Wiki Builder API (Laravel 12)

REST API for the Wiki Builder project, generated from the MPD schema with business rules and course-oriented security.

## Target stack

- Laravel 12
- PHP 8.3+
- MySQL 8
- Docker Compose

## Project deliverables implemented

1. `docker-compose.yml` + `Dockerfile` + `.env.example`
2. MPD migrations only (no Laravel default users/cache/jobs migrations)
3. Seeders with required data volume
4. Eloquent models + relationships
5. `CheckApiKey` + `CheckRole` middlewares registered in `bootstrap/app.php`
6. Controllers + routes + FormRequests + API Resources
7. README with exact commands and curl examples

## Security model

- All routes are throttled with `throttle:60,1`.
- Write routes require `API-KEY` header (`api_keys.key`, `is_active=1`).
- Admin write routes also require `X-USER-ID` mapped to `users.role=admin`.
- Middleware aliases are registered in `bootstrap/app.php` (`check.api.key`, `check.role`).

## Database schema

- Fully MPD-driven schema.
- Includes:
  - `users` table matching MPD (`username`, `email`, `role`, timestamps).
  - `api_keys`.
  - Pivot tables.
  - Composite PK on `build_equipment_modifiers (build_id, equipment_item_id, equipment_modifier_id)`.
- No default Laravel migrations.

## Run with Docker

```bash
cp .env.example .env
docker compose up -d --build
```

## Exact commands (required)

```bash
php artisan migrate --force
php artisan db:seed --force
```

## Clean reset workflow

```bash
php artisan migrate:fresh --seed --force
```

## Base URL

`http://localhost:8000/api/v1`

## Windows PowerShell test flow (Herd Lite)

If `php` is not in PATH, use the Herd binary directly:

```powershell
$phpExe = "$env:USERPROFILE\.config\herd-lite\bin\php.exe"
& $phpExe -v
& $phpExe artisan --version
```

Prepare local SQLite flow:

```powershell
New-Item -ItemType File -Force .\database\database.sqlite
& $phpExe artisan migrate:fresh --seed
& $phpExe artisan route:list
```

Start API:

```powershell
& $phpExe artisan serve
```

Get seeded API key:

```powershell
& $phpExe artisan tinker --execute='dump(\App\Models\ApiKey::query()->first()?->key);'
```

Test write route (expected `401` if `API-KEY` is missing):

```powershell
Invoke-RestMethod -Method Post `
  -Uri "http://127.0.0.1:8000/api/v1/builds" `
  -ContentType "application/json" `
  -Body '{"game_id":1,"title":"Test build","build_profile_id":1,"equipment_items":[{"equipment_item_id":1,"position":1}]}'
```

Test write route with API key:

```powershell
$apiKey = "dev-wiki-builder-key-1234567890abcdef"
Invoke-RestMethod -Method Post `
  -Uri "http://127.0.0.1:8000/api/v1/builds" `
  -Headers @{ "API-KEY" = $apiKey } `
  -ContentType "application/json" `
  -Body '{
    "game_id": 1,
    "title": "Test build",
    "description": "demo",
    "build_profile_id": 1,
    "equipment_items": [
      { "equipment_item_id": 1, "position": 1 },
      { "equipment_item_id": 2, "position": 2 }
    ]
  }'
```

Summary endpoint:

```powershell
Invoke-RestMethod "http://127.0.0.1:8000/api/v1/builds/4/summary" | ConvertTo-Json -Depth 10
```

## Seeded credentials

- `API-KEY`: `dev-wiki-builder-key-1234567890abcdef`
- Admin user id: `1` (`role=admin`)
- Standard user id: `2` (`role=user`)

## Curl examples

### Public read endpoints

```bash
curl -s "http://localhost:8000/api/v1/games"
```

```bash
curl -s "http://localhost:8000/api/v1/games/1/equipment-items?slot=weapon&rarity=epic&score_min=40&sort_by=score&sort_dir=desc&per_page=10"
```

```bash
curl -s "http://localhost:8000/api/v1/builds/1/summary"
```

### Create build (API key required)

```bash
curl -s -X POST "http://localhost:8000/api/v1/builds" \
  -H "Content-Type: application/json" \
  -H "API-KEY: dev-wiki-builder-key-1234567890abcdef" \
  -d '{
    "game_id": 1,
    "user_id": 2,
    "build_profile_id": 1,
    "title": "My Build",
    "description": "Test build",
    "equipment_items": [
      { "equipment_item_id": 1, "position": 1 },
      { "equipment_item_id": 2, "position": 2 }
    ],
    "equipment_modifiers": [
      { "equipment_item_id": 1, "equipment_modifier_id": 1 }
    ]
  }'
```

### Admin write endpoint (API key + X-USER-ID required)

```bash
curl -s -X POST "http://localhost:8000/api/v1/equipment-items" \
  -H "Content-Type: application/json" \
  -H "API-KEY: dev-wiki-builder-key-1234567890abcdef" \
  -H "X-USER-ID: 1" \
  -d '{
    "game_id": 1,
    "name": "New Helmet",
    "slot": "helmet",
    "rarity": "rare",
    "tier_level": "T2",
    "score": 55
  }'
```

### Compare builds

```bash
curl -s -X POST "http://localhost:8000/api/v1/builds/compare" \
  -H "Content-Type: application/json" \
  -d '{
    "left_build_id": 1,
    "right_build_id": 2
  }'
```

## Notes

- If you use local PHP instead of Docker, ensure PHP 8.3+ and MySQL 8 are available.
- Responses are standardized via `ApiResponse::success()` and `ApiResponse::error()`.
- `api/v1/builds/<ID>/summary` now returns `404` because route params are constrained to numeric IDs.
- If you run SQLite locally, keep `CACHE_STORE=file` in `.env` to avoid `no such table: cache` on `artisan optimize:clear`.
