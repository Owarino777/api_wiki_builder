# Wiki Builder API

## Démarrage rapide

```powershell
Copy-Item .env.example .env
```

Crée la configuration locale.

```bash
docker compose up -d --build
```

Construit les images, démarre `db`, `app`, `web`, génère la clé Laravel (si nécessaire) et exécute les migrations.

```bash
docker compose exec app php artisan db:seed --force
```

Charge les données de démonstration :
- jeux
- items
- modifiers
- catégories
- builds
- dataset BO7 offline
- utilisateur admin
- clé API de démo

## URLs utiles

- `http://localhost:8000` : interface web
- `http://localhost:8000/api/v1` : racine API JSON
- `http://localhost:8000/up` : healthcheck Laravel

## Commandes utiles

```bash
docker compose exec app php vendor/bin/pest
```

Exécute les tests (PHP 8.4 dans le conteneur).

```bash
docker compose exec app php artisan migrate --force
```

Relance les migrations sans reset.

```bash
docker compose exec app php artisan codmunity:import-bo7 --fresh
```

Import BO7 avec fallback automatique vers le dataset offline si CODMunity est indisponible.

```bash
docker compose exec app php artisan codmunity:import-bo7 --fresh --strict
```

Import BO7 strict : échec explicite si CODMunity est indisponible.

```bash
docker compose logs -f app
docker compose down
docker compose down -v
```

Logs, arrêt des conteneurs, puis arrêt + suppression des volumes.

## Exemples API

```bash
curl http://localhost:8000/api/v1
```

Affiche la racine API.

```bash
curl "http://localhost:8000/api/v1/games/1/items?per_page=20&sort_by=score&sort_dir=desc"
```

Liste les items du jeu `1`.

```powershell
curl.exe -X POST http://localhost:8000/api/v1/builds `
  -H "Content-Type: application/json" `
  -H "API-KEY: dev-wiki-builder-key-1234567890abcdef" `
  -d "{\"game_id\":1,\"title\":\"BO3 Flex\",\"items\":[{\"item_id\":1,\"position\":1}]}"
```

Crée un build de test.

## Important

- Ne pas utiliser `php artisan ...` directement sur Windows si PHP local n’est pas en `8.4`.
- Le projet requiert PHP `^8.4` : utiliser Docker pour l’app, les migrations et les tests.

## Workflow BO7 recommandé

### Démo stable (sans dépendance réseau)

```bash
docker compose exec app php artisan db:seed --force
```

### Dataset BO7 complet (réseau requis)

```bash
docker compose exec app php artisan codmunity:import-bo7 --fresh --strict
```

Si erreur `cURL error 35`, vérifier l’accès à `codmunity.gg` / `api.codmunity.gg` ou changer de réseau (ex. partage 4G/5G).

### Mode tolérant (fallback offline automatique)

```bash
docker compose exec app php artisan codmunity:import-bo7 --fresh
```

Après un import BO7 complet réussi, ne pas relancer `db:seed --force` pour éviter d’écraser les données importées.
