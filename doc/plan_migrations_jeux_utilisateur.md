# Plan de migrations - Feature jeux utilisateur

## 1. Objectif

Ce document transforme la spec produit/backend-first en plan de migrations concret, ordonne et implementable sans casser l'API existante.

Le plan cible :

- un runtime propre pour les jeux deja utilisables ;
- un schema draft complet pour la creation guidee ;
- une evolution de `games` pour ownership/visibilite ;
- une evolution de `categories` vers un scope par jeu.

## 2. Principes de migration

Regles a respecter pendant l'implementation :

- migrations de schema non destructives d'abord ;
- migration de donnees explicite et verifiable ensuite ;
- resserrement des contraintes seulement apres backfill ;
- aucune publication partielle ;
- aucun endpoint existant casse tant que la bascule n'est pas terminee.

## 3. Ordre recommande

### Phase A - Preparer le runtime

1. etendre `games`
2. preparer `categories` au scope par jeu

### Phase B - Creer le schema draft

3. `game_drafts`
4. `game_draft_steps`
5. `game_draft_archetypes`
6. `game_draft_categories`
7. `game_draft_items`
8. `game_draft_item_slots`
9. `game_draft_modifiers`
10. `game_draft_item_categories`
11. `game_draft_item_modifiers`

### Phase C - Migrer la donnee existante

12. backfill `games`
13. dupliquer et relier les `categories` par jeu
14. rebrancher `item_categories`

### Phase D - Resserer les contraintes

15. rendre `categories.game_id` obligatoire
16. poser les nouvelles unicites finales

## 4. Detail migration par migration

## M-001 - `add_creation_metadata_to_games_table`

### But

Rendre `games` compatible avec :

- ownership
- distinction systeme/utilisateur
- visibilite
- publication

### Colonnes a ajouter

- `owner_user_id` `BIGINT UNSIGNED NULL`
- `creation_category` `VARCHAR(40) NULL`
- `source_type` `VARCHAR(20) NOT NULL DEFAULT 'system'`
- `visibility` `VARCHAR(20) NOT NULL DEFAULT 'public'`
- `published_at` `TIMESTAMP NULL`

### Contraintes

- FK `owner_user_id -> users.id` `ON DELETE SET NULL`
- index sur `owner_user_id`
- index sur `source_type`
- index sur `visibility`
- index compose recommande sur `(source_type, visibility)`

### Backfill

- jeux existants : `source_type = 'system'`
- jeux existants : `visibility = 'public'`
- jeux existants : `published_at = created_at`
- jeux existants : `owner_user_id = null`
- jeux existants : `creation_category = null`

### Impact code

- `Game` model fillable/casts
- filtres de lecture dans `GameController`

## M-002 - `add_game_scope_columns_to_categories_table_phase_1`

### But

Preparer `categories` a devenir game-scoped sans casser la donnee existante.

### Colonnes a ajouter

- `game_id` `BIGINT UNSIGNED NULL`
- `source_type` `VARCHAR(20) NOT NULL DEFAULT 'system'`

### Contraintes temporaires

- FK `game_id -> games.id` `ON DELETE CASCADE`
- index sur `game_id`
- index compose sur `(game_id, slug)`

### Contraintes a conserver temporairement

- ancienne unicite globale sur `slug`
- ancienne unicite globale sur `label`

### Pourquoi en deux temps

On ne peut pas rendre `game_id` obligatoire tout de suite, car il faut d'abord redistribuer les categories existantes par jeu.

## M-003 - `create_game_drafts_table`

### Colonnes

- `id`
- `owner_user_id` `BIGINT UNSIGNED NOT NULL`
- `published_game_id` `BIGINT UNSIGNED NULL`
- `category_key` `VARCHAR(40) NOT NULL`
- `template_version` `VARCHAR(20) NOT NULL`
- `status` `VARCHAR(20) NOT NULL DEFAULT 'draft'`
- `current_step` `VARCHAR(40) NOT NULL DEFAULT 'foundation'`
- `name` `VARCHAR(120) NOT NULL`
- `slug` `VARCHAR(120) NOT NULL`
- `description` `TEXT NULL`
- `visibility` `VARCHAR(20) NOT NULL DEFAULT 'private'`
- `completion_percent` `UNSIGNED SMALLINT NULL`
- `last_validated_at` `TIMESTAMP NULL`
- `published_at` `TIMESTAMP NULL`
- `settings` `JSON NULL`
- `meta` `JSON NULL`
- timestamps

### Contraintes

- FK `owner_user_id -> users.id` `ON DELETE CASCADE`
- FK `published_game_id -> games.id` `ON DELETE SET NULL`
- unique `(owner_user_id, slug)` recommande
- unique `published_game_id` recommande si un draft publie pointe vers un seul runtime
- index `(owner_user_id, status)`
- index `(owner_user_id, updated_at)`
- index `category_key`
- index `current_step`

## M-004 - `create_game_draft_steps_table`

### Colonnes

- `id`
- `game_draft_id`
- `step_key` `VARCHAR(40) NOT NULL`
- `status` `VARCHAR(20) NOT NULL DEFAULT 'not_started'`
- `blocking_errors_count` `UNSIGNED SMALLINT NOT NULL DEFAULT 0`
- `warnings_count` `UNSIGNED SMALLINT NOT NULL DEFAULT 0`
- `last_saved_at` `TIMESTAMP NULL`
- `last_validated_at` `TIMESTAMP NULL`
- `payload_snapshot` `JSON NULL`
- timestamps

### Contraintes

- FK `game_draft_id -> game_drafts.id` `ON DELETE CASCADE`
- unique `(game_draft_id, step_key)`
- index `(game_draft_id, status)`

## M-005 - `create_game_draft_archetypes_table`

### Colonnes

- `id`
- `game_draft_id`
- `name` `VARCHAR(120) NOT NULL`
- `slug` `VARCHAR(140) NOT NULL`
- `description` `TEXT NULL`
- timestamps

### Contraintes

- FK `game_draft_id -> game_drafts.id` `ON DELETE CASCADE`
- unique `(game_draft_id, slug)`
- unique `(game_draft_id, name)`

## M-006 - `create_game_draft_categories_table`

### Colonnes

- `id`
- `game_draft_id`
- `label` `VARCHAR(80) NOT NULL`
- `slug` `VARCHAR(100) NOT NULL`
- `description` `TEXT NULL`
- timestamps

### Contraintes

- FK `game_draft_id -> game_drafts.id` `ON DELETE CASCADE`
- unique `(game_draft_id, slug)`
- unique `(game_draft_id, label)`

## M-007 - `create_game_draft_items_table`

### Colonnes

- `id`
- `game_draft_id`
- `name` `VARCHAR(140) NOT NULL`
- `slug` `VARCHAR(160) NOT NULL`
- `slot` `VARCHAR(60) NOT NULL`
- `rarity` `VARCHAR(40) NOT NULL`
- `tier_level` `VARCHAR(20) NULL`
- `score` `INT NOT NULL DEFAULT 0`
- `attack` `INT NULL`
- `defense` `INT NULL`
- `utility` `INT NULL`
- `accuracy` `INT NULL`
- `mobility` `INT NULL`
- `metadata` `JSON NULL`
- timestamps

### Contraintes

- FK `game_draft_id -> game_drafts.id` `ON DELETE CASCADE`
- unique `(game_draft_id, slug)`
- index `(game_draft_id, slot)`
- index `(game_draft_id, score)`

## M-008 - `create_game_draft_item_slots_table`

### Colonnes

- `id`
- `game_draft_item_id`
- `slot_key` `VARCHAR(60) NOT NULL`
- `slot_label` `VARCHAR(80) NOT NULL`
- `accepted_modifier_type` `VARCHAR(60) NULL`
- `sort_order` `UNSIGNED SMALLINT NOT NULL DEFAULT 1`
- `is_required` `BOOLEAN NOT NULL DEFAULT false`
- `metadata` `JSON NULL`
- timestamps

### Contraintes

- FK `game_draft_item_id -> game_draft_items.id` `ON DELETE CASCADE`
- unique `(game_draft_item_id, slot_key)`
- index `(game_draft_item_id, sort_order)`
- index `accepted_modifier_type`

## M-009 - `create_game_draft_modifiers_table`

### Colonnes

- `id`
- `game_draft_id`
- `name` `VARCHAR(140) NOT NULL`
- `slug` `VARCHAR(160) NOT NULL`
- `modifier_type` `VARCHAR(60) NOT NULL`
- `description` `TEXT NULL`
- `delta_score` `INT NOT NULL DEFAULT 0`
- `delta_attack` `INT NULL`
- `delta_defense` `INT NULL`
- `delta_utility` `INT NULL`
- `delta_accuracy` `INT NULL`
- `delta_mobility` `INT NULL`
- `metadata` `JSON NULL`
- timestamps

### Contraintes

- FK `game_draft_id -> game_drafts.id` `ON DELETE CASCADE`
- unique `(game_draft_id, slug)`
- unique `(game_draft_id, name)`
- index `(game_draft_id, modifier_type)`

## M-010 - `create_game_draft_item_categories_table`

### Colonnes

- `game_draft_item_id`
- `game_draft_category_id`

### Contraintes

- FK `game_draft_item_id -> game_draft_items.id` `ON DELETE CASCADE`
- FK `game_draft_category_id -> game_draft_categories.id` `ON DELETE CASCADE`
- PK composee `(game_draft_item_id, game_draft_category_id)`
- index `game_draft_category_id`

## M-011 - `create_game_draft_item_modifiers_table`

### Colonnes

- `game_draft_item_id`
- `game_draft_modifier_id`

### Contraintes

- FK `game_draft_item_id -> game_draft_items.id` `ON DELETE CASCADE`
- FK `game_draft_modifier_id -> game_draft_modifiers.id` `ON DELETE CASCADE`
- PK composee `(game_draft_item_id, game_draft_modifier_id)`
- index `game_draft_modifier_id`

## 5. Migration de donnees `categories`

## 5.1 Pourquoi cette migration est sensible

Aujourd'hui, `categories` est globale.

Demain, elle doit etre scopee au jeu.

Le probleme est que la table pivot `item_categories` reference encore des categories partagees entre plusieurs jeux.

Il faut donc dupliquer les categories existantes par jeu avant de poser la contrainte finale.

## 5.2 Strategie recommandee

La migration de donnees ne doit pas vivre en bloc dans une migration SQL aveugle.

Je recommande :

- une commande artisan dediee
- un service de migration de donnees
- une execution verifiable

Nom recommande :

- `php artisan app:migrate-categories-to-game-scope`

## 5.3 Algorithme de backfill

1. lire toutes les liaisons `item_categories`
2. joindre `items` pour recuperer `items.game_id`
3. pour chaque couple distinct `(game_id, old_category_id)` :
   - creer ou retrouver une nouvelle categorie scopee au jeu avec meme `label` et `slug`
4. reconstruire les lignes de `item_categories` vers les nouveaux `category_id`
5. verifier qu'aucune liaison ne pointe vers une categorie d'un autre jeu
6. supprimer les anciennes categories globales devenues inutiles

## 5.4 Verification obligatoire apres backfill

Verifier :

- aucun `item_categories` orphelin
- toute categorie runtime a un `game_id`
- aucun couple `(game_id, slug)` duplique
- les counts de liaisons avant/apres sont identiques

## 6. Migration finale de verrouillage `categories`

## M-012 - `finalize_categories_game_scope`

### Operations

- supprimer l'unicite globale sur `slug`
- supprimer l'unicite globale sur `label`
- rendre `game_id` `NOT NULL`
- poser `unique (game_id, slug)`
- poser `unique (game_id, label)` si tu veux interdire deux labels identiques dans le meme jeu

### Cible finale recommandee

`categories`

- `id`
- `game_id` `BIGINT UNSIGNED NOT NULL`
- `label` `VARCHAR(80) NOT NULL`
- `slug` `VARCHAR(100) NOT NULL`
- timestamps

Contraintes finales :

- FK `game_id -> games.id` `ON DELETE CASCADE`
- unique `(game_id, slug)`
- unique `(game_id, label)`

## 7. Donnees existantes et compatibilite

## 7.1 Jeux systeme existants

Les jeux deja seedes restent valides.

Ils doivent etre backfilles avec :

- `source_type = system`
- `visibility = public`
- `published_at = created_at`

## 7.2 Builds existants

Les builds existants n'ont pas besoin de migration directe pour cette feature.

Leur compatibilite reste intacte si :

- les `items`
- les `modifiers`
- les pivots de build

ne changent pas de cle primaire.

## 8. Impacts code a prevoir juste apres les migrations

Ce plan de migrations implique ensuite :

- mise a jour du model `Game`
- mise a jour du model `Tag`
- ajout des models draft
- adaptation du `TagController`
- adaptation du `EquipmentItemController`
- nouveaux services de progression/publication
- nouveaux `FormRequest`
- nouveaux `Resources`

## 9. Risques de mise en oeuvre

Les points les plus sensibles sont :

- la migration de donnees `categories`
- l'ownership si l'auth n'est pas renforcee avant les endpoints draft
- le risque de rendre publics des jeux utilisateurs par erreur si `visibility` est mal gere

## 10. Recommandation immediate

La bonne suite maintenant est :

1. implementer `M-001`
2. implementer `M-002`
3. implementer `M-003` a `M-011`
4. coder la commande de backfill `categories`
5. seulement ensuite verrouiller `M-012`

Ne pas commencer par `M-012` directement.
