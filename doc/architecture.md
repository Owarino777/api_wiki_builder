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

## 10. Jeux pre-charges

Le systeme embarque 5 jeux avec donnees realistes au demarrage :

| Jeu | Slug | Genre | Template | Items | Source |
|---|---|---|---|---|---|
| Call of Duty: Black Ops 7 | `call-of-duty-black-ops-7` | Arena Shooter | FPS | 50 armes | API CODMunity (snapshot) |
| Battlefield 2042 | `battlefield-2042` | First-Person Shooter | FPS | 6 armes | Seeder offline |
| Valorant | `valorant` | Tactical Shooter | FPS | 6 armes | Seeder offline |
| League of Legends | `league-of-legends` | MOBA | RPG | 6 champions | Seeder offline |
| New World | `new-world` | MMORPG | RPG | 6 armes | Seeder offline |

### Sources de donnees

- **BO7** : API publique `api.codmunity.gg`, snapshot JSON pre-genere et compresse (10 Mo) dans `docker/app/bo7-snapshot.zip`.
- **Battlefield 2042** : `BattlefieldSeeder` - armes reelles (M5A3, AK-24, PP-29, DM7, SWS-10, MCS-880) avec accessoires FPS (optiques, bouches, canons, poignees).
- **Valorant** : `ValorantSeeder` - armes du jeu (Vandal, Phantom, Operator, Spectre, Sheriff, Odin) avec modifications conceptuelles.
- **League of Legends** : `LeagueOfLegendsSeeder` - champions populaires (Jinx, Ahri, Yasuo, Thresh, Lee Sin, Darius) avec objets et runes comme modificateurs RPG.
- **New World** : `NewWorldSeeder` - armes du MMO (Sword and Shield, Great Axe, Fire Staff, Life Staff, Bow, War Hammer) avec gemmes et enchantements RPG.

### Demarrage rapide

Tous les seeders sont idempotents et s'executent automatiquement via `SEED_DATABASE=true` (defaut dans docker-compose.yml).
Le snapshot BO7 est extrait au boot et importe en ~12 secondes.
Les 4 autres jeux sont seeds instantanement depuis les fixtures PHP.
