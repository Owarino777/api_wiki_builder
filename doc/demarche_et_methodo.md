# Démarches de construction de l'API Wiki Builder — Guide complet

## Phase 0 — Installation du projet Laravel

```bash
composer create-project laravel/laravel api_wiki_builder
cd api_wiki_builder
```

Configurer le .env :
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wiki_game_builder
DB_USERNAME=root
DB_PASSWORD=
```

Générer la clé application :
```bash
php artisan key:generate
```

**Supprimer les migrations Laravel par défaut** (fichiers dans migrations) :
```bash
# Supprimer manuellement les fichiers :
# - 0001_01_01_000000_create_users_table.php
# - 0001_01_01_000001_create_cache_table.php
# - 0001_01_01_000002_create_jobs_table.php
```

---

## Phase 1 — Migrations (1 fichier par table, ordre des FK respecté)

Chaque commande génère un squelette vide à remplir avec le schéma MPD.

```bash
php artisan make:migration create_api_keys_table
php artisan make:migration create_users_table
php artisan make:migration create_games_table
php artisan make:migration create_build_profiles_table
php artisan make:migration create_equipment_items_table
php artisan make:migration create_equipment_modifiers_table
php artisan make:migration create_tags_table
php artisan make:migration create_builds_table
php artisan make:migration create_equipment_item_tags_table
php artisan make:migration create_equipment_item_compatible_modifiers_table
php artisan make:migration create_build_equipment_items_table
php artisan make:migration create_build_equipment_modifiers_table
```

**Points importants dans chaque migration :**
- Tables pivot : `->onDelete('cascade')` sur les FK
- `build_equipment_items.position` : `unsignedSmallInteger` (pas `smallInteger`)
- `build_equipment_modifiers` : PK composite sur `(build_id, equipment_item_id, equipment_modifier_id)`
- Tables avec noms longs : nommer explicitement les FK et index pour ne pas dépasser 64 caractères MySQL (ex: `indexName: 'eicm_modifier_fk'`)

Lancer les migrations :
```bash
php artisan migrate
```

Reset complet :
```bash
php artisan migrate:fresh
```

---

## Phase 2 — Modèles Eloquent

```bash
php artisan make:model ApiKey
php artisan make:model User          # déjà existant, à modifier
php artisan make:model Game
php artisan make:model BuildProfile
php artisan make:model EquipmentItem
php artisan make:model EquipmentModifier
php artisan make:model Tag
php artisan make:model Build
```

**Relations à implémenter dans chaque modèle :**

| Modèle | Relations |
|---|---|
| `Game` | `hasMany` → BuildProfile, EquipmentItem, EquipmentModifier, Build |
| `BuildProfile` | `belongsTo` → Game ; `hasMany` → Build |
| `EquipmentItem` | `belongsTo` → Game ; `belongsToMany` → Tag (pivot `equipment_item_tags`) ; `belongsToMany` → EquipmentModifier (pivot `equipment_item_compatible_modifiers`) |
| `EquipmentModifier` | `belongsTo` → Game ; `belongsToMany` → EquipmentItem (pivot `equipment_item_compatible_modifiers`) |
| `Tag` | `belongsToMany` → EquipmentItem |
| `Build` | `belongsTo` → Game, User (nullable), BuildProfile (nullable) ; `belongsToMany` → EquipmentItem (pivot `build_equipment_items` avec `position`) ; `belongsToMany` → EquipmentModifier (pivot `build_equipment_modifiers` avec `equipment_item_id`) |
| `User` | `hasMany` → Build |
| `ApiKey` | aucune relation, `$table = 'api_keys'` |

**Pour chaque modèle** : définir `$fillable`, `$casts` (ex: `metadata → array`), et les relations Eloquent.

---

## Phase 3 — Seeders

```bash
php artisan make:seeder WikiBuilderSeeder
```

Modifier DatabaseSeeder.php pour appeler le seeder :
```php
$this->call([WikiBuilderSeeder::class]);
```

**Données à seeder (selon le cahier des charges) :**
- 2 games
- 5 build_profiles (répartis entre les 2 jeux)
- 20 equipment_items (10 par jeu)
- 20 equipment_modifiers (10 par jeu)
- 8 tags
- Lignes pivot : equipment_item_tags, equipment_item_compatible_modifiers
- 2 users (1 admin + 1 user)
- 1 api_key active
- 2 builds avec leurs items et modifiers

Lancer le seed :
```bash
php artisan db:seed
```

Ou tout en un :
```bash
php artisan migrate:fresh --seed
```

---

## Phase 4 — Support (ApiResponse)

Créer manuellement ApiResponse.php (pas de commande artisan pour ça).

Deux méthodes statiques :
- `ApiResponse::success($data, $message, $status)` → `{"success": true, "message": "...", "data": ...}`
- `ApiResponse::error($message, $status, $errors)` → `{"success": false, "message": "...", "errors": ...}`

---

## Phase 5 — Form Requests (validation)

```bash
php artisan make:request ApiFormRequest
php artisan make:request StoreBuildRequest
php artisan make:request UpdateBuildRequest
php artisan make:request StoreBuildProfileRequest
php artisan make:request UpdateBuildProfileRequest
php artisan make:request StoreEquipmentItemRequest
php artisan make:request UpdateEquipmentItemRequest
php artisan make:request StoreEquipmentModifierRequest
php artisan make:request UpdateEquipmentModifierRequest
php artisan make:request CompareBuildRequest
```

**`ApiFormRequest`** est la classe de base qui override `failedValidation()` pour retourner une erreur JSON via `ApiResponse::error()` au lieu du comportement HTML par défaut de Laravel.

Toutes les autres requests étendent `ApiFormRequest` au lieu de `FormRequest`.

---

## Phase 6 — Resources (transformation JSON)

```bash
php artisan make:resource GameResource
php artisan make:resource BuildProfileResource
php artisan make:resource EquipmentItemResource
php artisan make:resource EquipmentModifierResource
php artisan make:resource TagResource
php artisan make:resource BuildResource
```

Chaque resource définit `toArray()` pour contrôler les champs exposés. Utiliser `$this->whenLoaded()` pour les relations conditionnelles.

---

## Phase 7 — Services (logique métier)

Créer manuellement (pas de commande artisan) :

- BuildRuleService.php — valide les 3 règles métier :
  1. Build mono-jeu (items, modifiers et profile même `game_id`)
  2. Modifier compatible avec l'item (vérification dans `equipment_item_compatible_modifiers`)
  3. Modifier référence un item présent dans le build

- BuildSummaryService.php — calcule :
  - `summary()` : SUM des stats items + SUM des delta modifiers (NULL traité comme 0 via COALESCE)
  - `compare()` : diff entre deux builds

---

## Phase 8 — Controllers

```bash
php artisan make:controller Api/GameController
php artisan make:controller Api/BuildProfileController
php artisan make:controller Api/EquipmentItemController
php artisan make:controller Api/EquipmentModifierController
php artisan make:controller Api/TagController
php artisan make:controller Api/BuildController
php artisan make:controller Api/EquipmentCompatibilityController
php artisan make:controller Api/EquipmentItemTagController
```

**Méthodes par controller :**

| Controller | Méthodes |
|---|---|
| `GameController` | `index`, `show`, `buildProfiles`, `equipmentItems`, `equipmentModifiers` |
| `BuildProfileController` | `store`, `update`, `destroy` |
| `EquipmentItemController` | `index`, `show`, `store`, `update`, `destroy` |
| `EquipmentModifierController` | `index`, `show`, `store`, `update`, `destroy` |
| `TagController` | `index` |
| `BuildController` | `show`, `store`, `update`, `destroy`, `summary`, `compare` |
| `EquipmentCompatibilityController` | `attach`, `detach` |
| `EquipmentItemTagController` | `attach`, `detach` |

**Filtres/tri/pagination** sur `EquipmentItemController` et `EquipmentModifierController` :
- Filtres : `game_id`, `slot`, `rarity`, `score_min`, `score_max`, `tag`, `modifier_type`
- Tri : `sort_by` + `sort_dir`
- Pagination : `per_page` (max 100)

---

## Phase 9 — Middlewares

```bash
php artisan make:middleware CheckApiKey
php artisan make:middleware CheckRole
```

**`CheckApiKey`** :
- Lit le header `API-KEY`
- Vérifie en base : `ApiKey::where('key', $key)->where('is_active', true)`
- Retourne `401` si absent/invalide

**`CheckRole`** :
- Lit le header `X-USER-ID`
- Charge le user en base, vérifie `role`
- Retourne `403` si non autorisé

**Enregistrement dans app.php** (Laravel 12, pas de Kernel.php) :
```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'check.api.key' => CheckApiKey::class,
        'check.role' => CheckRole::class,
    ]);
})
```

---

## Phase 10 — Routes (api.php)

Structure :
```
/api/v1/
├── GET  /games                          (public)
├── GET  /games/{id}                     (public)
├── GET  /games/{id}/build-profiles      (public)
├── GET  /games/{id}/equipment-items     (public)
├── GET  /games/{id}/equipment-modifiers (public)
├── GET  /tags                           (public)
├── GET  /equipment-items                (public)
├── GET  /equipment-items/{id}           (public)
├── GET  /equipment-modifiers            (public)
├── GET  /equipment-modifiers/{id}       (public)
├── GET  /builds/{id}                    (public)
├── GET  /builds/{id}/summary            (public)
├── POST /builds/compare                 (public)
│
├── [middleware: check.api.key]
│   ├── POST/PUT/DELETE /builds          (API Key)
│   │
│   └── [middleware: check.role:admin]
│       ├── POST/PUT/DELETE /build-profiles
│       ├── POST/PUT/DELETE /equipment-items
│       ├── POST/PUT/DELETE /equipment-modifiers
│       ├── POST/DELETE /equipment-items/{id}/compatible-modifiers/{id}
│       └── POST/DELETE /equipment-items/{id}/tags/{id}
```

Toutes les routes sont wrappées dans `middleware('throttle:60,1')`.

---

## Phase 11 — Docker

Créer Dockerfile + docker-compose.yml (services app + `db`).

```bash
docker compose up -d --build
docker compose exec app php artisan migrate:fresh --seed --force
```

---

## Phase 12 — Vérifications finales

```bash
# Lister toutes les routes
php artisan route:list

# Tester les endpoints
# Public
curl -s "http://localhost:8000/api/v1/games"
curl -s "http://localhost:8000/api/v1/builds/1/summary"

# Write (401 attendu sans API-KEY)
curl -s -X POST "http://localhost:8000/api/v1/builds" \
  -H "Content-Type: application/json" \
  -d '{"game_id":1,"title":"Test"}'

# Write avec API-KEY
curl -s -X POST "http://localhost:8000/api/v1/builds" \
  -H "Content-Type: application/json" \
  -H "API-KEY: dev-wiki-builder-key-1234567890abcdef" \
  -d '{"game_id":1,"title":"Test","equipment_items":[{"equipment_item_id":1,"position":1}]}'

# Admin (403 attendu sans X-USER-ID admin)
curl -s -X POST "http://localhost:8000/api/v1/equipment-items" \
  -H "Content-Type: application/json" \
  -H "API-KEY: dev-wiki-builder-key-1234567890abcdef" \
  -H "X-USER-ID: 1" \
  -d '{"game_id":1,"name":"Test Sword","slot":"weapon","rarity":"rare","score":50}'
```

---

## Récapitulatif des commandes artisan utilisées

| Commande | Nombre | Fichiers générés |
|---|---|---|
| `make:migration` | 12 | 12 fichiers migrations |
| `make:model` | 7 (+1 existant) | 8 modèles |
| `make:seeder` | 1 | 1 seeder |
| `make:request` | 10 | 10 form requests |
| `make:resource` | 6 | 6 resources |
| `make:controller` | 8 | 8 controllers |
| `make:middleware` | 2 | 2 middlewares |
| **Créés manuellement** | — | ApiResponse.php, `BuildRuleService.php`, `BuildSummaryService.php` |

**Total : 46 commandes artisan + 3 fichiers manuels**