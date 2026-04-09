# Wiki Builder API

## Version et périmètre

- version du projet : Laravel 12
- version PHP requise : `^8.4`
- voie d'exécution de référence : Docker

Références officielles :

- installation Laravel 12 : https://laravel.com/docs/12.x/installation
- configuration Laravel 12 : https://laravel.com/docs/12.x/configuration
- configuration Laravel 13 : https://laravel.com/docs/13.x/configuration
- starter kits Laravel : https://laravel.com/docs/13.x/starter-kits

Documentation de rendu professeur :

- [doc/rendu_final_prof.md](doc/rendu_final_prof.md)
- [doc/livrables/README.md](doc/livrables/README.md)

## Prérequis

- Docker Desktop
- Docker Compose
- si tu exécutes PHP hors Docker : PHP 8.4

Important :

- le projet déclare `php ^8.4` dans `composer.json`
- si ta machine locale est en PHP 8.2, privilégie Docker
- pour le rendu, la procédure de référence est donc `docker compose up -d --build`

## Démarrage express

### Cas du ZIP de rendu

Dans le ZIP final, le fichier `.env` et le snapshot BO7 sont déjà présents.

```powershell
docker compose up -d --build
```

Au premier démarrage, la commande peut prendre un peu de temps le temps de :

- construire l'image PHP
- démarrer `db`, `app` et `web`
- générer `APP_KEY` si besoin
- lancer les migrations
- créer ou recréer la base MySQL Docker
- importer le vrai dataset BO7 depuis le snapshot local embarqué

### Cas d'un clone Git

Si tu pars d'un clone sans `.env`, fais d'abord :

```powershell
Copy-Item .env.example .env
docker compose up -d --build
```

### Arrêter Docker

Conserver les données :

```powershell
docker compose down
```

Supprimer aussi le volume MySQL :

```powershell
docker compose down -v
```

Au prochain `docker compose up -d --build`, la base sera recréée automatiquement.

Note :

- `docker compose down -v` supprime les volumes du projet, sauf si un conteneur one-shot créé avec `docker compose run` les utilise encore
- si les services tournent déjà, préfère toujours `docker compose exec ...` à `docker compose run ...`
- si tu dois lancer un conteneur ponctuel, préfère `docker compose run --rm ...` pour qu'il soit supprimé automatiquement à la fin

## Ce que contient le ZIP

Le ZIP doit contenir :

- le code source complet
- le dossier `doc/`
- le fichier `.env`
- la configuration Docker
- le snapshot BO7 local dans `storage/app/private/codmunity/`
- le dump SQL `dump/wiki_builder.sql` à titre d'archive de rendu

Important :

- le ZIP n'embarque pas le volume MySQL Docker en cours d'exécution
- sur une autre machine, la base est reconstruite automatiquement par les migrations au démarrage
- le dataset BO7 réel est ensuite réinjecté depuis le snapshot local inclus

## Paramètres de démarrage par défaut

Par défaut, Docker démarre avec :

- `SEED_DATABASE=false`
- `BOOTSTRAP_BO7_MODE=live`
- `BOOTSTRAP_BO7_SOURCE=prefer-snapshot`
- `BOOTSTRAP_BO7_FORCE=false`

Ce comportement signifie :

- le projet ne charge pas de seed démo au démarrage
- il charge le vrai dataset BO7 depuis le snapshot local inclus
- si le snapshot manque, il peut retomber sur un import live CODMunity

## URLs

- `http://localhost:8000` : interface web intégrée
- `http://localhost:8000/api/v1` : API JSON
- `http://localhost:8000/up` : healthcheck Laravel

## Front

Il n'y a pas de front séparé à installer.

Le front est intégré dans Laravel :

- routes web : `routes/web.php`
- contrôleur principal : `app/Http/Controllers/HomeController.php`
- page d'accueil : `resources/views/welcome.blade.php`

Donc :

- pas de `npm install`
- pas de `npm run dev`
- pas de front React ou Vue séparé
- il suffit d'ouvrir `http://localhost:8000`

## Jeu de données BO7

### Comportement par défaut

```powershell
docker compose up -d --build
```

Au premier démarrage ou après un `docker compose down -v` :

- le snapshot local BO7 est importé automatiquement
- la base est repeuplée sans commande manuelle supplémentaire

### Régénérer le snapshot live

Si tu veux rafraîchir le snapshot avec les données CODMunity les plus récentes :

```powershell
docker compose exec app php artisan codmunity:import-bo7 --fresh --source=live --strict
```

### Réutiliser le snapshot local

Le snapshot BO7 est stocké dans :

- `storage/app/private/codmunity/bo7-live-snapshot.json`
- `storage/app/private/codmunity/bo7-live-snapshot/`

Tu peux le réinjecter sans repasser par le réseau :

```powershell
docker compose exec app php artisan bo7:ensure-data --mode=live --source=snapshot --force
```

Ou faire un import direct depuis ce snapshot :

```powershell
docker compose exec app php artisan codmunity:import-bo7 --fresh --source=snapshot --strict
```

### Garantir le vrai dataset BO7

```powershell
docker compose exec app php artisan bo7:ensure-data --mode=live --source=prefer-snapshot
```

Cette commande :

- ne fait rien si BO7 live est déjà chargé
- restaure BO7 depuis le snapshot s'il est disponible
- retente le live si nécessaire

Quand le stack Docker est déjà démarré, garde cette forme avec `docker compose exec app ...`.
N'utilise `docker compose run app ...` que pour un one-shot hors stack, et dans ce cas préfère `docker compose run --rm app ...` pour éviter de laisser un conteneur arrêté qui bloque ensuite `docker compose down -v`.

### Seed de développement optionnel

```powershell
docker compose exec app php artisan db:seed --force
```

Ce seed reste utile pour des démonstrations de développement, mais il n'est pas utilisé au démarrage par défaut.

Attention :

- `db:seed --force` recharge le dataset de démonstration du projet
- il peut remplacer le vrai BO7 par un BO7 offline de démonstration
- si cela arrive, relance ensuite :

```powershell
docker compose exec app php artisan bo7:ensure-data --mode=live --source=prefer-snapshot --force
```

## Auth et écritures

Par défaut, le projet démarre sans utilisateur de démonstration ni clé API injectée automatiquement.

Conséquences :

- la lecture publique BO7 fonctionne immédiatement
- l'interface d'auth web fonctionne
- les pages web utiles sont accessibles via `/login`, `/register`, `/dashboard`, `/account` et `/privacy`
- les écritures API depuis la page d'accueil nécessitent un utilisateur et une clé API déjà présents en base

Si tu veux démontrer rapidement les routes d'écriture avec des comptes déjà prêts, tu peux lancer le seed de développement :

```powershell
docker compose exec app php artisan db:seed --force
```

Le seed crée alors deux comptes de démonstration :

- `admin@wikibuilder.local` / `builder123`
- `player1@wikibuilder.local` / `builder123`

Et deux clés API de démonstration :

- `dev-admin-key-1234567890abcdef`
- `dev-player-key-1234567890abcdef`

## Réseau école et erreur TLS

Si tu vois :

```text
cURL error 35: OpenSSL SSL_connect: SSL_ERROR_SYSCALL
```

Le problème vient du réseau vers `codmunity.gg`, pas du projet.

Dans ce cas :

- utilise le snapshot local déjà inclus
- ou passe sur un autre réseau
- si le réseau est lent mais pas bloqué, augmente `CODMUNITY_REQUEST_TIMEOUT` dans `.env`

## Import déjà en cours

Si tu relances un import BO7 alors que le bootstrap du conteneur `app` est encore en train de travailler, la commande peut répondre :

```text
A BO7 dataset import is already running. Wait for it to finish before launching another one.
```

Dans ce cas :

- attends la fin du démarrage du conteneur
- puis relance la commande si nécessaire

## Volume Docker encore utilisé

Si `docker compose down -v` finit par :

```text
Resource is still in use
```

la cause la plus fréquente est un conteneur one-shot déjà arrêté, créé avec `docker compose run`, qui garde encore le volume `vendor_data`.

Nettoyage minimal :

```powershell
docker rm bo7-diagnose
docker compose down -v
```

Si tu relances un diagnostic manuel, préfère :

```powershell
docker compose run --rm app php artisan bo7:ensure-data --mode=live --source=prefer-snapshot
```

## Tests

Le projet utilise Pest v4, les dépendances de test ne sont pas dans l'image runtime.

Un service Docker dédié `test` est fourni avec un `Dockerfile.dev` qui installe les dépendances dev :

```powershell
docker compose --profile testing run --rm test
```

Au premier lancement, l'image dev sera construite et les dépendances complètes installées.

La base de test `wiki_builder_test` est créée automatiquement par le script `docker/db/init-test-db.sql` au premier démarrage du service `db`.

Si le service `db` existe déjà sans la base de test, fais un `docker compose down -v` puis `docker compose up -d --build` pour que l'init SQL soit rejoué.

## Commandes utiles

```powershell
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force
docker compose exec app php artisan bo7:ensure-data --mode=live --source=prefer-snapshot
docker compose exec app php artisan bo7:ensure-data --mode=live --source=snapshot --force
docker compose exec app php artisan bo7:ensure-data --mode=offline --force
docker compose exec app php artisan codmunity:import-bo7 --fresh --source=live --strict
docker compose exec app php artisan codmunity:import-bo7 --fresh --source=snapshot --strict
docker compose logs -f app
docker compose down
docker compose down -v
```

## Variables BO7

Dans `.env` :

```dotenv
SEED_DATABASE=false
BOOTSTRAP_BO7_MODE=live
BOOTSTRAP_BO7_SOURCE=prefer-snapshot
BOOTSTRAP_BO7_FORCE=false
CODMUNITY_REQUEST_TIMEOUT=120
CODMUNITY_CONNECT_TIMEOUT=15
CODMUNITY_RETRY_ATTEMPTS=3
CODMUNITY_RETRY_SLEEP_MS=500
```

Valeurs possibles pour `BOOTSTRAP_BO7_MODE` :

- `live` : charge BO7 depuis le snapshot si disponible, sinon tente le live
- `live-strict` : import live obligatoire, échec si CODMunity est inaccessible
- `offline` : garantit uniquement le dataset offline
- `skip` : ne fait rien au démarrage

Valeurs possibles pour `BOOTSTRAP_BO7_SOURCE` :

- `prefer-snapshot` : utilise d'abord le snapshot local
- `live` : télécharge toujours depuis CODMunity
- `snapshot` : utilise uniquement le snapshot local

Variables CODMunity :

- `CODMUNITY_REQUEST_TIMEOUT` : timeout total HTTP
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
  -H "API-KEY: your-api-key" `
  -d "{\"game_id\":1,\"title\":\"BO7 Flex\",\"items\":[{\"item_id\":1,\"position\":1}]}"
```

## À retenir

- pour le rendu prof : `docker compose up -d --build`
- pour ouvrir l'interface : `http://localhost:8000`
- le ZIP n'embarque pas la base Docker en cours, mais la base est reconstruite automatiquement
- le vrai dataset BO7 est restauré depuis le snapshot local inclus
- pour régénérer le snapshot live : `php artisan codmunity:import-bo7 --fresh --source=live --strict`
