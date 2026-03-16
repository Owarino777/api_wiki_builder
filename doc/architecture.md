# Documentation d'architecture - API Wiki Builder

## 1. Statut

Ce document decrit l'architecture cible effectivement implementee dans le projet apres refactor.

Reference schema detaillee :
- `doc/architecture_modifications.md`

## 2. Objectif produit

L'API doit couvrir :
- un catalogue multi-jeux
- une fiche detaillee par item
- un builder de builds/equipements piloté par la base

Le projet doit rester data-driven :
- nouveaux jeux sans code specifique
- nouveaux types de slots sans `if/else` par jeu
- nouveaux types de modificateurs sans remanier le code applicatif

## 3. Concepts centraux

### 3.1 Multi-jeux

Tout est rattache a `games` :
- `archetypes`
- `items`
- `modifiers`
- `builds`

### 3.2 Deux niveaux de slots

- `items.slot` = emplacement global
- `item_slots` = emplacements internes d'accessoires

### 3.3 Builder data-driven

Le builder repose sur :
- `item_modifiers` pour la compatibilite
- `item_slots` pour les emplacements disponibles
- `build_slots` pour l'equipement effectif dans un build

## 4. Entites et pivots

Entites :
- `games`
- `users`
- `api_keys`
- `archetypes`
- `items`
- `item_slots`
- `modifiers`
- `categories`
- `builds`

Pivots :
- `item_categories`
- `item_modifiers`
- `build_items`
- `build_slots`

## 5. API

Routes publiques principales :
- `GET /api/v1/games`
- `GET /api/v1/games/{id}`
- `GET /api/v1/games/{id}/archetypes`
- `GET /api/v1/games/{id}/items`
- `GET /api/v1/games/{id}/modifiers`
- `GET /api/v1/categories`
- `GET /api/v1/items`
- `GET /api/v1/items/{id}`
- `GET /api/v1/modifiers`
- `GET /api/v1/modifiers/{id}`
- `GET /api/v1/builds`
- `GET /api/v1/builds/{id}`
- `GET /api/v1/builds/{id}/summary`
- `POST /api/v1/builds/compare`
- `GET /api/v1/builds/share/{shareToken}`

## 6. Regles metier

- build mono-jeu
- archetype du meme jeu que le build
- item du meme jeu que le build
- modifier du meme jeu que le build
- compatibilite item/modifier obligatoire
- item_slot obligatoire pour equiper un modifier
- type du modifier conforme au slot
- un seul modifier par slot

## 7. Securite

- `API-KEY` pour les routes d'ecriture
- `X-USER-ID` pour le role
- throttle global
- reponses JSON standardisees

## 8. RGPD

Le projet integre un perimetre RGPD reel :
- export utilisateur
- anonymisation utilisateur
- suppression utilisateur

Les donnees personnelles sont stockees dans `users` de maniere volontaire pour l'evaluation pedagogique.

## 9. Extension prevue - Jeux utilisateur

La creation de jeux par l'utilisateur ne doit pas ecrire directement dans le runtime existant.

La cible retenue pour cette extension est :

- creation guidee dans un schema `draft`
- validation backend forte par etape
- publication transactionnelle vers le runtime
- jeux utilisateur prives par defaut en V1
- ownership derive du contexte d'auth

Documents de reference pour cette extension :

- `doc/spec_ajout_jeux_utilisateur.md`
- `doc/adr_001_jeux_utilisateur_draft_runtime.md`
- `doc/plan_migrations_jeux_utilisateur.md`

Point d'attention important :

- `categories` devra evoluer vers un scope par jeu pour eviter les collisions entre datasets utilisateurs.
