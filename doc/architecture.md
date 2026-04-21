# Architecture — API Wiki Builder

> Document de référence unique. Consolide architecture.md, architecture_modifications.md, wiki_game_builder.md, demarche_et_methodo.md et AI_INSTRUCTIONS.md.
> Diagrammes UML et base de données : `doc/diagrammes.md`.
> Feature création de jeux utilisateur : `doc/creation_jeux.md`.

---

## 1. Objectif produit

L'API est un **wiki builder multi-jeux data-driven**. Elle permet :

- d'exposer un catalogue multi-jeux (jeux, archetypes, items, modifiers, categories)
- d'afficher le détail d'un item avec ses slots internes et ses modificateurs compatibles
- de construire des builds avec contraintes métier réelles
- de comparer et partager des builds

Principe fondamental : ajouter un nouveau jeu ne doit pas demander de modifier le code. Tout se porte en base via `item_slots.slot_key`, `item_slots.accepted_modifier_type` et `modifiers.modifier_type`.

---

## 2. Stack technique

- **Laravel 12 / PHP 8.4** — framework backend
- **MySQL 8** — base de données
- **Docker Compose** — orchestration locale et production
- **Vite** — assets frontend (Blade vanilla, pas de SPA)

---

## 3. Vocabulaire retenu

| Ancien nom | Nom cible |
|---|---|
| `build_profiles` | `archetypes` |
| `equipment_items` | `items` |
| `equipment_modifiers` | `modifiers` |
| `tags` | `categories` |
| `equipment_item_tags` | `item_categories` |
| `equipment_item_compatible_modifiers` | `item_modifiers` |
| `build_equipment_items` | `build_items` |
| `build_equipment_modifiers` | `build_slots` |

> Les modèles Eloquent conservent provisoirement les anciens noms (`BuildProfile`, `EquipmentItem`…).

---

## 4. Entités et pivots

| Table | Rôle |
|---|---|
| `games` | Catalogue des jeux disponibles |
| `users` | Comptes utilisateurs (enrichi RGPD) |
| `api_keys` | Clés d'accès en écriture |
| `archetypes` | Profils / classes par jeu |
| `items` | Armes, objets, champions par jeu |
| `item_slots` | Emplacements internes d'un item (pour modificateurs) |
| `modifiers` | Accessoires, runes, effets par jeu |
| `categories` | Classification des items, scopée au jeu |
| `builds` | Builds/équipements d'un utilisateur |
| `item_categories` | Items ↔ categories |
| `item_modifiers` | Compatibilité explicite item ↔ modifier |
| `build_items` | Items équipés dans un build |
| `build_slots` | Slot précis occupé par un modifier dans un build |

### Deux niveaux de slots

- `items.slot` = emplacement **global** dans le build (ex : `weapon`)
- `item_slots` = emplacements **internes** de l'item (ex : `optic`, `magazine`)

Ces deux concepts ne font pas doublon.

---

## 5. Modèle de données (MPD)

### `games`
```
id                BIGINT UNSIGNED  PK AI
name              VARCHAR(120)     NOT NULL
slug              VARCHAR(120)     NOT NULL UNIQUE
genre             VARCHAR(80)      NULL
owner_user_id     BIGINT UNSIGNED  NULL  FK -> users.id ON DELETE SET NULL
creation_category VARCHAR(40)      NULL
source_type       VARCHAR(20)      NOT NULL DEFAULT 'system'
visibility        VARCHAR(20)      NOT NULL DEFAULT 'public'
published_at      TIMESTAMP        NULL
```

### `items`
```
id            BIGINT UNSIGNED  PK AI
game_id       BIGINT UNSIGNED  NOT NULL  FK -> games.id
name          VARCHAR(140)     NOT NULL
slug          VARCHAR(160)     NOT NULL
external_ref  VARCHAR(160)     NULL
slot          VARCHAR(60)      NOT NULL
rarity        VARCHAR(40)      NOT NULL
tier_level    VARCHAR(20)      NULL
score         INT              NOT NULL DEFAULT 0
attack/defense/utility/accuracy/mobility  INT  NULL
metadata      JSON             NULL
UNIQUE (game_id, slug)
```

### `item_slots`
```
id                      BIGINT UNSIGNED   PK AI
item_id                 BIGINT UNSIGNED   NOT NULL  FK -> items.id
slot_key                VARCHAR(60)       NOT NULL
slot_label              VARCHAR(80)       NOT NULL
accepted_modifier_type  VARCHAR(60)       NULL
sort_order              SMALLINT UNSIGNED NOT NULL DEFAULT 1
is_required             TINYINT(1)        NOT NULL DEFAULT 0
UNIQUE (item_id, slot_key)
```

### `modifiers`
```
id             BIGINT UNSIGNED  PK AI
game_id        BIGINT UNSIGNED  NOT NULL  FK -> games.id
name           VARCHAR(140)     NOT NULL
slug           VARCHAR(160)     NOT NULL
modifier_type  VARCHAR(60)      NOT NULL
delta_score/attack/defense/utility/accuracy/mobility  INT  NULL
UNIQUE (game_id, slug)
```

### `categories`
```
id       BIGINT UNSIGNED  PK AI
game_id  BIGINT UNSIGNED  NOT NULL  FK -> games.id ON DELETE CASCADE
label    VARCHAR(80)      NOT NULL
slug     VARCHAR(100)     NOT NULL
UNIQUE (game_id, slug)
UNIQUE (game_id, label)
```

### `builds`
```
id            BIGINT UNSIGNED  PK AI
game_id       BIGINT UNSIGNED  NOT NULL  FK -> games.id
user_id       BIGINT UNSIGNED  NULL      FK -> users.id
archetype_id  BIGINT UNSIGNED  NULL      FK -> archetypes.id
title         VARCHAR(140)     NOT NULL
share_token   VARCHAR(64)      NULL UNIQUE
is_public     TINYINT(1)       NOT NULL DEFAULT 0
```

### Pivots
```
item_categories : PK (item_id, category_id)
item_modifiers  : PK (item_id, modifier_id)
build_items     : PK (build_id, item_id) | position SMALLINT NULL
build_slots     : PK (build_id, item_id, item_slot_id) | modifier_id FK modifiers.id
```

---

## 6. Routes API

### Lecture publique
```
GET /api/v1/games
GET /api/v1/games/{id}
GET /api/v1/games/{id}/archetypes
GET /api/v1/games/{id}/items
GET /api/v1/games/{id}/modifiers
GET /api/v1/categories
GET /api/v1/items
GET /api/v1/items/{id}
GET /api/v1/modifiers
GET /api/v1/modifiers/{id}
GET /api/v1/builds
GET /api/v1/builds/{id}
GET /api/v1/builds/{id}/summary
GET /api/v1/builds/share/{shareToken}
POST /api/v1/builds/compare
```

### Écriture (nécessite API-KEY)
```
POST /api/v1/archetypes
POST /api/v1/categories
POST /api/v1/items
POST /api/v1/modifiers
POST /api/v1/items/{itemId}/modifiers/{modifierId}
POST /api/v1/items/{itemId}/categories/{categoryId}
POST /api/v1/builds
```

### Studio — Création de jeux
```
GET  /studio/api/v1/game-categories
POST /studio/api/v1/game-drafts
GET|PATCH /studio/api/v1/game-drafts/{id}/foundation
GET|PATCH /studio/api/v1/game-drafts/{id}/structure
POST      /studio/api/v1/game-drafts/{id}/publish
CRUD      /studio/api/v1/game-drafts/{id}/archetypes|categories|items|modifiers
```

---

## 7. Règles métier (non négociables)

1. Un build est mono-jeu.
2. Archetype, item et modifier doivent appartenir au même jeu que le build.
3. Un modifier ne peut être équipé que si la compatibilité existe dans `item_modifiers`.
4. Un modifier doit être équipé via un `item_slot_id` valide appartenant à l'item ciblé.
5. `accepted_modifier_type` doit correspondre à `modifiers.modifier_type` (NULL = wildcard).
6. Un seul modifier par slot dans un build.
7. Comparaison de builds réservée à deux builds du même jeu.
8. Résumé de stats = stats item + deltas modifiers (NULL = 0).

---

## 8. Sécurité

- `API-KEY` header obligatoire pour les routes d'écriture (`check.api.key`)
- User résolu depuis le contexte d'auth, **jamais** depuis le payload
- Throttle global sur l'API
- Réponses JSON via `ApiResponse` (`{ success, message, data }`)

---

## 9. RGPD

Table `users` enrichie pour :
- export des données personnelles
- anonymisation (flag `is_anonymized`, champs identifiants nullifiés)
- suppression (FK builds conservées pour historique fonctionnel)

---

## 10. Jeux pré-chargés

| Jeu | Template | Items | Source |
|---|---|---|---|
| Call of Duty: Black Ops 7 | FPS | 50 armes | Snapshot API CODMunity (`docker/app/bo7-snapshot.zip`) |
| Battlefield 2042 | FPS | 6 armes | `BattlefieldSeeder` |
| Valorant | FPS | 6 armes | `ValorantSeeder` |
| League of Legends | RPG | 6 champions | `LeagueOfLegendsSeeder` |
| New World | RPG | 6 armes | `NewWorldSeeder` |

Seeders idempotents, exécutés automatiquement via `SEED_DATABASE=true`. Snapshot BO7 importé en ~12 s au boot.

---

## 11. Conventions de développement

- Validation via `ApiFormRequest` — jamais inline
- Sérialisation via `Resources`
- Logique métier dans des Services (`BuildRuleService`, `GameDraftValidationService`…)
- Middlewares custom : `check.api.key`, `require.request.user`
- 1 migration par table, seeders héritent de `AbstractGameSeeder`

---

## 12. Ordre de migration

1. `api_keys` → `users` → `games` → `archetypes` → `items` → `item_slots`
2. `modifiers` → `categories` → `builds`
3. `item_categories` → `item_modifiers` → `build_items` → `build_slots`
4. Tables draft (`game_drafts`, `game_draft_steps`, `game_draft_archetypes`, `game_draft_categories`, `game_draft_items`, `game_draft_item_slots`, `game_draft_modifiers`, pivots)
