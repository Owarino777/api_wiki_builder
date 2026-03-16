# Wiki Builder API

## Commandes de base

`Copy-Item .env.example .env`

Crée le fichier de config local.

`docker compose up -d --build`

Construit l'image, démarre `db`, `app`, `web`, génère la clé Laravel si besoin et lance les migrations.

`docker compose exec app php artisan db:seed --force`

Charge les données de démo :
- jeux
- items
- modifiers
- categories
- builds
- BO7 hors ligne
- utilisateur admin
- clé API de démo

## URLs à ouvrir

`http://localhost:8000`

Interface web du builder.

`http://localhost:8000/api/v1`

Racine JSON de l'API.

`http://localhost:8000/up`

Healthcheck Laravel.

## Commandes utiles

`docker compose exec app php vendor/bin/pest`

Lance les tests dans le conteneur PHP 8.4.

`docker compose exec app php artisan migrate --force`

Relance les migrations sans reset.

`docker compose exec app php artisan codmunity:import-bo7 --fresh`

Tente l'import complet BO7 depuis CODMunity. Si CODMunity est indisponible ou si le TLS plante, la commande ne casse plus et recharge automatiquement le dataset offline BO7.

`docker compose exec app php artisan codmunity:import-bo7 --fresh --strict`

Import BO7 en mode strict : échoue avec code erreur si CODMunity est indisponible.

`docker compose logs -f app`

Affiche les logs Laravel / PHP.

`docker compose down`

Arrête les conteneurs.

`docker compose down -v`

Arrête les conteneurs et supprime les volumes Docker, donc la base locale.

## Commandes de démo API

`curl http://localhost:8000/api/v1`

Affiche la racine API et les endpoints utiles.

`curl "http://localhost:8000/api/v1/games/1/items?per_page=20&sort_by=score&sort_dir=desc"`

Liste les items du jeu `1`.

```powershell
curl.exe -X POST http://localhost:8000/api/v1/builds `
  -H "Content-Type: application/json" `
  -H "API-KEY: dev-wiki-builder-key-1234567890abcdef" `
  -d "{\"game_id\":1,\"title\":\"BO3 Flex\",\"items\":[{\"item_id\":1,\"position\":1}]}"
```

Crée un build de test.

## Important

N'utilise pas `php artisan ...` directement sur ton Windows si ton PHP local n'est pas en `8.4`.

Le projet demande PHP `^8.4`, donc passe par Docker pour lancer l'app, les migrations et les tests.

Pour BO7 :
- `docker compose exec app php artisan db:seed --force` charge le dataset offline de démo
- `docker compose exec app php artisan codmunity:import-bo7 --fresh` tente l'import complet en ligne avec fallback offline automatique
- `docker compose exec app php artisan codmunity:import-bo7 --fresh --strict` force un vrai échec si l'import réseau ne passe pas

## Workflow BO7 recommandé

Pour une démo garantie :
- `docker compose exec app php artisan db:seed --force`

Pour récupérer le vrai dataset BO7 complet :
- `docker compose exec app php artisan codmunity:import-bo7 --fresh --strict`

Si `--strict` échoue avec `cURL error 35` :
- le réseau bloque probablement `codmunity.gg` ou `api.codmunity.gg`
- essaie un autre réseau, par exemple un partage de connexion 4G/5G

Si tu veux une démo qui ne casse jamais, même sans réseau :
- `docker compose exec app php artisan codmunity:import-bo7 --fresh`
- cette commande bascule automatiquement sur le dataset offline BO7 si CODMunity est inaccessible

Important après un import complet BO7 réussi :
- ne relance pas `docker compose exec app php artisan db:seed --force`
- sinon tu remplaces le dataset BO7 complet par le dataset offline de démo
