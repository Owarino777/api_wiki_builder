# Wiki Builder API

## Version et perimetre

- version projet : Laravel 12
- version officielle plus recente disponible cote documentation : Laravel 13
- decision actuelle : rester sur Laravel 12 pour stabiliser le rendu, tout en documentant l'existence de Laravel 13

References officielles :

- installation Laravel 12 : https://laravel.com/docs/12.x/installation
- configuration Laravel 12 : https://laravel.com/docs/12.x/configuration
- configuration Laravel 13 : https://laravel.com/docs/13.x/configuration
- starter kits Laravel : https://laravel.com/docs/13.x/starter-kits

Documentation de rendu professeur :

- [rendu_final_prof.md](c:/Users/malik/M1_DFS_2025_2026/DEV/api_wiki_builder/doc/rendu_final_prof.md)

## Prerequis

- Docker Desktop
- Docker Compose
- si tu lances PHP hors Docker : PHP 8.4 requis par le projet

Important :

- le projet declare `php ^8.4` dans `composer.json`
- si ta machine locale est en PHP 8.2, privilegie Docker pour l'execution
- la voie de reference pour le rendu est donc l'execution via Docker

## Lancer le projet
(ceci suppose que le lancement est effectué via un clone du repo GitHub, pas via un téléchargement ZIP)
```powershell
Copy-Item .env.example .env
docker compose up -d --build
```

pour fermer docker sans perdre les données :
```powershell
docker compose down
```
pour fermer docker et supprimer les données :
```powershell
docker compose down -v
```

Ce que fait `docker compose up -d --build` :
- build l'image PHP
- démarre `db`, `app`, `web`
- génère `APP_KEY` si besoin
- lance les migrations
- garantit un dataset BO7 au démarrage

Par défaut, Docker démarre avec :
- `BOOTSTRAP_BO7_MODE=live`
- si CODMunity répond : import BO7 complet
- si CODMunity ne répond pas : fallback BO7 offline
- si un dataset BO7 live existe déjà : il n'est pas écrasé

## URLs

- `http://localhost:8000` : interface web intégrée
- `http://localhost:8000/api/v1` : API JSON
- `http://localhost:8000/up` : healthcheck Laravel

## Front

Il n'y a pas de front séparé à installer.

Le front de démo est intégré dans Laravel :
- route web : [routes/web.php](c:/Users/malik/M1_DFS_2025_2026/DEV/api_wiki_builder/routes/web.php)
- controller : [HomeController.php](c:/Users/malik/M1_DFS_2025_2026/DEV/api_wiki_builder/app/Http/Controllers/HomeController.php)
- vue : [welcome.blade.php](c:/Users/malik/M1_DFS_2025_2026/DEV/api_wiki_builder/resources/views/welcome.blade.php)

Donc :
- pas de `npm install`
- pas de `npm run dev`
- pas de front React/Vue séparé
- tu ouvres juste `http://localhost:8000`

## Orientation auth / parcours utilisateur

Pour faire evoluer le projet vers un vrai parcours utilisateur avec :

- inscription / connexion / deconnexion
- espace personnel
- creation de jeux
- creation d'equipements
- creation et comparaison de builds

la direction recommandee est :

- Laravel full-stack
- starter kit Livewire
- pas de front separe

Cette evolution n'est pas obligatoire pour demarrer le projet actuel, mais c'est la direction la plus coherente si le rendu final inclut une vraie IHM d'authentification et de creation guidee.

## Données

### Dataset minimal garanti au démarrage Docker

```powershell
docker compose up -d --build
```

Au premier démarrage ou si BO7 est absent :
- mode `live` : tentative d'import complet CODMunity
- si échec réseau/TLS : BO7 offline chargé automatiquement

### Dataset de démonstration complet

```powershell
docker compose exec app php artisan db:seed --force
```

Ce que ça charge :
- jeux BO3 + Steel Horizon
- utilisateur admin + utilisateur standard
- clé API de démo
- catégories, items, modifiers, builds
- BO7 offline

Attention :
- `db:seed --force` remet BO7 sur le dataset offline de démo
- si tu avais un BO7 complet importé depuis CODMunity, il sera remplacé

### Restaurer ou garantir le vrai dataset BO7

```powershell
docker compose exec app php artisan bo7:ensure-data --mode=live
```

Ce que fait cette commande :
- si BO7 live existe déjà : ne fait rien
- si BO7 est absent : importe le dataset live
- si BO7 est retombé sur l'offline : tente de remettre le live
- si CODMunity ne répond pas : recharge l'offline au lieu de casser

### Forcer le rechargement BO7

```powershell
docker compose exec app php artisan bo7:ensure-data --mode=live --force
```

### Import strict CODMunity

```powershell
docker compose exec app php artisan codmunity:import-bo7 --fresh --strict
```

À utiliser si tu veux un échec explicite au lieu d'un fallback offline.

## Réseau école / erreur TLS

Si tu vois :

```text
cURL error 35: OpenSSL SSL_connect: SSL_ERROR_SYSCALL
```

Le problème vient du réseau vers `codmunity.gg`, pas du projet.

Dans ce cas :
- utilise `bo7:ensure-data --mode=live` pour garder un fallback sûr
- ou passe sur un autre réseau / partage de connexion
- si le réseau est lent mais pas bloqué, augmente `CODMUNITY_REQUEST_TIMEOUT` dans `.env`

## Import déjà en cours

Si tu relances un import BO7 alors que Docker est encore en train d'exécuter le bootstrap BO7 au démarrage, la commande répond maintenant proprement au lieu de provoquer un lock MySQL.

Message attendu :

```text
A BO7 dataset import is already running. Wait for it to finish before launching another one.
```

Dans ce cas :
- attends la fin du démarrage du conteneur `app`
- puis relance la commande

## Commandes utiles

```powershell
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force
docker compose exec app php artisan bo7:ensure-data --mode=live
docker compose exec app php artisan bo7:ensure-data --mode=offline --force
docker compose exec app php artisan codmunity:import-bo7 --fresh --strict
docker compose exec app php artisan categories:backfill-game-scope --dry-run
docker compose logs -f app
docker compose down
docker compose down -v
```

## Variables Docker BO7

Dans `.env` local :

```dotenv
BOOTSTRAP_BO7_MODE=live
BOOTSTRAP_BO7_FORCE=false
CODMUNITY_REQUEST_TIMEOUT=120
CODMUNITY_CONNECT_TIMEOUT=15
CODMUNITY_RETRY_ATTEMPTS=3
CODMUNITY_RETRY_SLEEP_MS=500
```

Valeurs possibles pour `BOOTSTRAP_BO7_MODE` :
- `live` : import live si absent ou si BO7 est en offline, sinon ne touche à rien
- `live-strict` : import live obligatoire, échec si CODMunity est inaccessible
- `offline` : garantit seulement le dataset offline
- `skip` : ne fait rien au démarrage

`BOOTSTRAP_BO7_FORCE=true` :
- force un refresh BO7 au démarrage
- à éviter en local si tu veux conserver un import déjà fait

Variables CODMunity :
- `CODMUNITY_REQUEST_TIMEOUT` : timeout total HTTP, utile si le payload `attachments-data` est lent
- `CODMUNITY_CONNECT_TIMEOUT` : timeout de connexion initiale
- `CODMUNITY_RETRY_ATTEMPTS` : nombre de tentatives
- `CODMUNITY_RETRY_SLEEP_MS` : pause entre tentatives

## Exemples API

```powershell
curl.exe http://localhost:8000/api/v1
```

```powershell
curl.exe "http://localhost:8000/api/v1/games"
```

```powershell
curl.exe "http://localhost:8000/api/v1/categories?game_id=1"
```

```powershell
curl.exe -X POST http://localhost:8000/api/v1/builds `
  -H "Content-Type: application/json" `
  -H "API-KEY: dev-wiki-builder-key-1234567890abcdef" `
  -d "{\"game_id\":1,\"title\":\"BO3 Flex\",\"items\":[{\"item_id\":1,\"position\":1}]}"
```

## À retenir

- pour démarrer vite : `docker compose up -d --build`
- pour avoir tout le dataset de démo : `php artisan db:seed --force`
- si `db:seed` a remplacé ton vrai BO7 : `php artisan bo7:ensure-data --mode=live`
- pour voir l'interface : `http://localhost:8000`
