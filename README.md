# Wiki Builder API

API REST Laravel 12 pour un wiki builder multi-jeux.

Le projet couvre trois besoins clairs :
- exposer un catalogue de jeux, items, modifiers et categories
- retourner le detail complet d un item avec ses slots et ses compatibilites
- permettre de composer, resumer et comparer des builds

## Entrees utiles

- racine web : `/`
- root API JSON : `/api/v1`
- healthcheck Laravel : `/up`

La racine web sert de vitrine fonctionnelle : choix du jeu, filtre du catalogue, chargement de 1 a 3 armes, selection des modifiers par slot et lecture rapide des ecarts de stats.

## Docker local

1. `copy .env.example .env`
2. `docker compose up -d --build`
3. `docker compose exec app php artisan db:seed --force`

Notes :
- la cle app est generee automatiquement si `.env` existe et que `APP_KEY` est vide
- les migrations tournent au boot en local
- la base n est plus detruite a chaque redemarrage
- le port expose est `http://localhost:8000`
- l image Docker utilise PHP 8.4

## Docker prod

Prerequis :
- Docker Desktop demarre sur Windows ou macOS
- Docker Engine actif sur Linux
- le port `80` doit etre libre
- verifier `.env.production` avant le premier lancement, au minimum :
  `APP_URL`
  `DB_DATABASE`
  `DB_USERNAME`

Fichiers attendus apres preparation :
- `.env.production`
- `docker/secrets/app_key`
- `docker/secrets/db_password`
- `docker/secrets/db_root_password`

### Windows PowerShell

```powershell
Copy-Item .env.production.example .env.production -Force
notepad .env.production
$appKey = "base64:" + [Convert]::ToBase64String((1..32 | ForEach-Object { Get-Random -Maximum 256 }))
$dbPassword = -join ((48..57 + 65..90 + 97..122) | Get-Random -Count 24 | ForEach-Object { [char]$_ })
$dbRootPassword = -join ((48..57 + 65..90 + 97..122) | Get-Random -Count 24 | ForEach-Object { [char]$_ })
Set-Content docker/secrets/app_key $appKey
Set-Content docker/secrets/db_password $dbPassword
Set-Content docker/secrets/db_root_password $dbRootPassword
docker compose --env-file .env.production -f docker-compose.prod.yml up -d --build
docker compose --env-file .env.production -f docker-compose.prod.yml exec app php artisan migrate --force
docker compose --env-file .env.production -f docker-compose.prod.yml exec app php artisan db:seed --force
```

### macOS / Linux

```bash
cp .env.production.example .env.production
nano .env.production
openssl rand -base64 32 | sed 's/^/base64:/' > docker/secrets/app_key
openssl rand -base64 24 > docker/secrets/db_password
openssl rand -base64 24 > docker/secrets/db_root_password
docker compose --env-file .env.production -f docker-compose.prod.yml up -d --build
docker compose --env-file .env.production -f docker-compose.prod.yml exec app php artisan migrate --force
docker compose --env-file .env.production -f docker-compose.prod.yml exec app php artisan db:seed --force
```

Important :
- remplir `.env.production` avant `docker compose ... up -d --build`
- les 3 fichiers dans `docker/secrets/` sont generes localement
- ne commit jamais les secrets reels
- `db:seed --force` est utile pour charger le dataset de demo du builder

Notes :
- `APP_DEBUG=false`
- pas de `key:generate` automatique
- pas de `migrate:fresh`
- Nginx sert `public/` et passe PHP a `php-fpm`
- les secrets ne sont pas commits
- l image Docker utilise PHP 8.4

## API

Public :
- `GET /api/v1`
- `GET /api/v1/games`
- `GET /api/v1/games/{id}/archetypes`
- `GET /api/v1/games/{id}/items`
- `GET /api/v1/items/{id}`
- `GET /api/v1/builds`
- `GET /api/v1/builds/{id}/summary`
- `POST /api/v1/builds/compare`

Protege :
- header ecriture : `API-KEY`
- header admin : `X-USER-ID`

Exemple lecture :

```bash
curl http://localhost:8000/api/v1
curl "http://localhost:8000/api/v1/games/1/items?per_page=20&sort_by=score&sort_dir=desc"
```

Exemple creation de build :

```bash
curl -X POST http://localhost:8000/api/v1/builds \
  -H "Content-Type: application/json" \
  -H "API-KEY: dev-wiki-builder-key-1234567890abcdef" \
  -d "{\"game_id\":1,\"title\":\"BO3 Flex\",\"items\":[{\"item_id\":1,\"position\":1}]}"
```

## Coherence projet

Le projet reste bien aligne avec un objectif de builder :
- le modele est data-driven par jeu
- les slots internes vivent dans `item_slots`
- les compatibilites vivent dans `item_modifiers`
- les equips reels d un build vivent dans `build_slots`

Le schema detaille reste dans [doc/architecture_modifications.md](/c:/Users/malik/Dev/api_wiki_builder/doc/architecture_modifications.md).
