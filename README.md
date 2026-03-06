# Wiki Builder API

API REST Laravel 12 pour un **wiki builder multi-jeux**.

Le projet expose :
- un catalogue de jeux et de contenus
- un detail d'item enrichi
- un builder de builds pilote par la base
- un perimetre RGPD d'evaluation

## Modele de donnees

Noms cibles :
- `archetypes`
- `items`
- `item_slots`
- `modifiers`
- `categories`
- `item_categories`
- `item_modifiers`
- `build_items`
- `build_slots`

Le detail complet du schema est dans [architecture_modifications.md](/c:/Users/malik/M1_DFS_2025_2026/DEV/api_wiki_builder/doc/architecture_modifications.md).

## Installation

```bash
docker compose up -d --build
php artisan migrate --force
php artisan db:seed --force
php artisan migrate:fresh --seed --force
```

## Endpoints principaux

Lecture :
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

Administration :
- `POST /api/v1/archetypes`
- `POST /api/v1/categories`
- `POST /api/v1/items`
- `POST /api/v1/modifiers`

## Builder payload

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

## Regles metier

- build mono-jeu
- item et modifier du meme jeu que le build
- compatibilite via `item_modifiers`
- equipement d'un modifier via `item_slot_id`
- un seul modifier par slot
- comparaison reservee a deux builds du meme jeu

## RGPD

Routes admin :
- `GET /api/v1/users/{id}/export`
- `POST /api/v1/users/{id}/anonymize`
- `DELETE /api/v1/users/{id}`

La table `users` contient volontairement plus de champs personnels pour permettre une evaluation reelle de l'anonymisation.

## Tests

```bash
php artisan test
```
