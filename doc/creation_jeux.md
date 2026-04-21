# Création de jeux utilisateur — Spec, ADR et Plan de migrations

> Document de référence unique. Consolide spec_ajout_jeux_utilisateur.md, adr_001_jeux_utilisateur_draft_runtime.md et plan_migrations_jeux_utilisateur.md.

---

## 1. Résumé exécutif

La feature recommandée n'est pas un simple `POST /games`. Le bon design est un parcours en deux couches :

- une couche **draft** pour la création guidée et incomplète
- une couche **published** qui alimente les tables runtime existantes (`games`, `archetypes`, `items`, `item_slots`, `modifiers`, `categories`)

Cette séparation empêche un utilisateur de polluer l'API publique avec un jeu incohérent ou incomplet.

Décisions retenues :
- garder `games` comme source de vérité des jeux utilisables
- introduire un aggregate de brouillon (`game_drafts` + sous-entités)
- piloter la création par template (`fps`, `rpg`) définis côté backend
- autoriser les sauvegardes partielles
- interdire la publication tant que les règles structurelles ne sont pas satisfaites
- publier via une transaction unique qui convertit le draft en dataset runtime

---

## 2. Décisions d'architecture (ADR-001)

**Statut** : Retenu pour implémentation.  
**Date** : 2026-03-16

### Décisions retenues

1. **Séparation stricte draft / runtime** — les tables runtime restent réservées aux jeux exploitables.
2. **Publication transactionnelle** — soit tout le jeu runtime est créé, soit rien.
3. **Templates portés par le backend** — les contraintes `fps`/`rpg` vivent dans un registre versionné, pas en base.
4. **Ownership dérivé du contexte d'auth** — jamais depuis le payload.
5. **Jeux utilisateur privés par défaut** — n'apparaissent pas dans `GET /api/v1/games` public.
6. **Categories scopées au jeu** — unicité par `(game_id, slug)` pour éviter les collisions entre jeux utilisateurs.
7. **Pas d'édition structurelle destructive d'un jeu publié en V1**.

### Alternatives écartées

- Écriture directe dans `games/items/modifiers` → pollue le runtime
- Brouillon JSON unique dans une table → mauvais pour la validation et la publication
- Categories globales communes → incompatible avec les jeux utilisateurs hétérogènes
- Publication publique immédiate → trop risquée sans modération

---

## 3. Parcours utilisateur recommandé

Le parcours guide la création en étapes ordonnées selon les dépendances métier :

| Étape | Action | Objectif |
|---|---|---|
| 0 | Choix du template | `fps` ou `rpg`, versionnés côté backend |
| 1 | Init du draft | Nom, slug, template figé définitivement |
| 2 | Fondations | Nom final, description, visibilité |
| 3 | Cadre structurel | Slots autorisés, types de modifiers, capacités du jeu |
| 4 | Archetypes + categories locales | Créées avant les items pour éviter les orphelins |
| 5 | Items | Avec slots internes si `supports_modifiers = true` |
| 6 | Modifiers et compatibilités | Après les items car dépend d'eux |
| 7 | Revue et publication | Validation finale + transaction de promotion |

### Sauvegarde et reprise

- Chaque écriture sauvegarde immédiatement sans exiger la complétion totale
- `current_step` = premier step bloquant
- `category_key` et `template_version` sont immuables après création
- Modification du cadre structurel interdite si des items existent déjà

---

## 4. Minima de publication

### Template FPS
- 1 fondation complète + cadre structurel valide
- ≥ 1 archetype, ≥ 2 categories locales, ≥ 4 items (dont ≥ 1 `weapon`)
- Si `supports_modifiers = true` : ≥ 3 modifiers, chacun compatible avec ≥ 1 item

### Template RPG
- 1 fondation complète + cadre structurel valide
- ≥ 1 archetype, ≥ 2 categories locales, ≥ 6 items, ≥ 2 slots globaux actifs
- Si `supports_modifiers = true` : ≥ 3 modifiers compatibles, tous les items classifiés

---

## 5. Modèle de données draft

### `game_drafts`
```
id                BIGINT UNSIGNED  PK AI
owner_user_id     BIGINT UNSIGNED  NOT NULL  FK -> users.id ON DELETE CASCADE
published_game_id BIGINT UNSIGNED  NULL      FK -> games.id ON DELETE SET NULL
category_key      VARCHAR(40)      NOT NULL
template_version  VARCHAR(20)      NOT NULL
status            VARCHAR(20)      NOT NULL DEFAULT 'draft'
current_step      VARCHAR(40)      NOT NULL DEFAULT 'foundation'
name              VARCHAR(120)     NOT NULL
slug              VARCHAR(120)     NOT NULL
description       TEXT             NULL
visibility        VARCHAR(20)      NOT NULL DEFAULT 'private'
completion_percent UNSIGNED SMALLINT NULL
last_validated_at TIMESTAMP        NULL
published_at      TIMESTAMP        NULL
settings          JSON             NULL
meta              JSON             NULL
INDEX (owner_user_id, status)
INDEX (category_key)
```

### `game_draft_steps`
```
id                    BIGINT UNSIGNED  PK AI
game_draft_id         BIGINT UNSIGNED  NOT NULL  FK -> game_drafts.id ON DELETE CASCADE
step_key              VARCHAR(40)      NOT NULL
status                VARCHAR(20)      NOT NULL DEFAULT 'not_started'
blocking_errors_count UNSIGNED SMALLINT NOT NULL DEFAULT 0
warnings_count        UNSIGNED SMALLINT NOT NULL DEFAULT 0
last_saved_at         TIMESTAMP        NULL
last_validated_at     TIMESTAMP        NULL
payload_snapshot      JSON             NULL
UNIQUE (game_draft_id, step_key)
```

### Sous-entités de draft

```
game_draft_archetypes   : game_draft_id | name | slug | description
game_draft_categories   : game_draft_id | label | slug | description
game_draft_items        : game_draft_id | name | slug | slot | rarity | stats... | metadata
game_draft_item_slots   : game_draft_item_id | slot_key | slot_label | accepted_modifier_type | sort_order | is_required
game_draft_modifiers    : game_draft_id | name | slug | modifier_type | description | deltas...
```

### Pivots de draft
```
game_draft_item_categories : PK (game_draft_item_id, game_draft_category_id)
game_draft_item_modifiers  : PK (game_draft_item_id, game_draft_modifier_id)
```

---

## 6. Plan de migrations

### Phase A — Préparer le runtime

**M-001** `add_creation_metadata_to_games_table`
- Ajouter : `owner_user_id NULL`, `creation_category NULL`, `source_type DEFAULT 'system'`, `visibility DEFAULT 'public'`, `published_at NULL`
- Backfill : jeux existants → `source_type='system'`, `visibility='public'`, `published_at=created_at`

**M-002** `add_game_scope_columns_to_categories_table_phase_1`
- Ajouter : `game_id NULL`, `source_type DEFAULT 'system'`
- Ne pas encore rendre `game_id` obligatoire (backfill d'abord)

### Phase B — Créer le schéma draft

**M-003** `create_game_drafts_table`  
**M-004** `create_game_draft_steps_table`  
**M-005** `create_game_draft_archetypes_table`  
**M-006** `create_game_draft_categories_table`  
**M-007** `create_game_draft_items_table`  
**M-008** `create_game_draft_item_slots_table`  
**M-009** `create_game_draft_modifiers_table`  
**M-010** `create_game_draft_item_categories_table`  
**M-011** `create_game_draft_item_modifiers_table`

### Phase C — Migration données `categories`

Commande artisan dédiée : `php artisan app:migrate-categories-to-game-scope`

Algorithme :
1. Lire toutes les liaisons `item_categories`, joindre `items` pour `game_id`
2. Pour chaque couple `(game_id, old_category_id)` : créer une catégorie scopée au jeu
3. Reconstruire `item_categories` vers les nouveaux `category_id`
4. Vérifier : 0 orphelins, counts avant/après identiques

### Phase D — Verrouillage final

**M-012** `finalize_categories_game_scope`
- Supprimer unicité globale sur `slug` et `label`
- Rendre `game_id NOT NULL`
- Poser `UNIQUE (game_id, slug)` + `UNIQUE (game_id, label)`

> Ne pas commencer par M-012. Toujours faire le backfill avant.

---

## 7. Design API

### Endpoints

```
GET    /studio/api/v1/game-categories          # Templates disponibles
GET    /studio/api/v1/game-categories/{key}

GET    /studio/api/v1/game-drafts              # Mes drafts
POST   /studio/api/v1/game-drafts              # Créer un draft
GET    /studio/api/v1/game-drafts/{id}
DELETE /studio/api/v1/game-drafts/{id}

PATCH  /studio/api/v1/game-drafts/{id}/foundation
PATCH  /studio/api/v1/game-drafts/{id}/structure
POST   /studio/api/v1/game-drafts/{id}/steps/{stepKey}/validate
POST   /studio/api/v1/game-drafts/{id}/publish

CRUD   /studio/api/v1/game-drafts/{id}/archetypes
CRUD   /studio/api/v1/game-drafts/{id}/categories
CRUD   /studio/api/v1/game-drafts/{id}/items
CRUD   /studio/api/v1/game-drafts/{id}/modifiers
```

### Codes de statut

| Code | Cas |
|---|---|
| 200 | Lecture / mise à jour / validation |
| 201 | Création / publication réussie |
| 204 | Suppression |
| 401 | API key manquante |
| 403 | Non owner ou rôle insuffisant |
| 404 | Draft ou ressource introuvable |
| 409 | État incompatible (draft déjà publié, step verrouillé) |
| 422 | Validation ou règle métier violée |

### FormRequests nécessaires

`StoreGameDraftRequest`, `UpdateGameDraftFoundationRequest`, `UpdateGameDraftStructureRequest`, `StoreGameDraftItemRequest`, `StoreGameDraftModifierRequest`, `PublishGameDraftRequest`

---

## 8. Publication transactionnelle

La publication (`POST /game-drafts/{id}/publish`) doit :

1. Validation globale finale
2. Ouvrir une transaction
3. Créer le `game` runtime
4. Insérer `archetypes`, `categories`, `items`, `item_slots`, `modifiers`
5. Insérer les pivots `item_categories`, `item_modifiers`
6. Marquer le draft : `status = published`, `published_game_id`, `published_at`
7. Commit

En cas d'échec → rollback complet. Aucun résidu runtime ne doit subsister.

---

## 9. Risques principaux

- **Ownership faible** : si l'auth n'est pas renforcée avant ouverture de la feature, risque de sécurité direct
- **Categories globales** : M-002 → M-012 doit être fait avant tout jeu utilisateur en prod
- **RPG sur modèle de stats fixe** : V1 acceptable avec RPG cadré ; extension future pour stats configurables
- **Édition destructive d'un jeu publié** : interdite en V1 — prévoir une logique de révision pour V2
