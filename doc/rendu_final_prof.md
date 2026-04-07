# Rendu Final Prof

## Objectif

Ce document sert de checklist de rendu pour le ZIP final déposé sur Teams.

Nom attendu :

- `<nom_prenom.zip>`

Le ZIP doit contenir le projet complet avec :

- le code source
- le dossier `doc/`
- le `README.md`
- la configuration Docker
- le snapshot BO7 local
- le dump SQL `dump/wiki_builder.sql` à titre d'archive de rendu

Important :

- le ZIP n'embarque pas le volume MySQL Docker déjà lancé sur la machine de développement
- sur la machine du prof, la base sera recréée automatiquement au démarrage

## Vérification simple attendue

Le scénario cible pour le prof est :

```powershell
docker compose up -d --build
```

Puis ouvrir :

- `http://localhost:8000`
- `http://localhost:8000/api/v1`

Il ne doit pas y avoir d'autre commande obligatoire pour obtenir l'application fonctionnelle en lecture.

## Ce que fait le démarrage Docker

La commande `docker compose up -d --build` :

- construit l'image PHP
- démarre `db`, `app` et `web`
- génère `APP_KEY` si besoin
- lance les migrations
- recrée la base MySQL Docker si nécessaire
- importe automatiquement le dataset BO7 réel depuis le snapshot local inclus dans le projet

Conséquence :

- le prof n'a pas besoin de récupérer les données BO7 via Internet si le snapshot est présent dans le ZIP
- si le volume MySQL est supprimé, un nouveau démarrage reconstruit la base

## Version Laravel

État actuel du projet :

- application basée sur Laravel 12
- dépendance déclarée : `laravel/framework ^12.0`
- PHP requis par le projet : `^8.4`

Point de veille :

- la documentation officielle Laravel 13.x existe
- le projet n'a pas besoin de migrer vers Laravel 13 pour le rendu

Sources officielles consultées :

- Laravel 12 Installation : https://laravel.com/docs/12.x/installation
- Laravel 12 Configuration : https://laravel.com/docs/12.x/configuration
- Laravel 13 Configuration : https://laravel.com/docs/13.x/configuration
- Laravel Starter Kits : https://laravel.com/docs/13.x/starter-kits

Décision retenue :

- conserver Laravel 12 pour stabiliser le rendu
- documenter l'existence de Laravel 13 sans migrer à la dernière minute

## Auth et interface

État actuel implémenté :

- interface publique BO7 intégrée dans Laravel
- inscription, connexion et déconnexion web
- dashboard utilisateur
- création de drafts de jeux utilisateur
- publication transactionnelle draft vers runtime

Choix actuel :

- auth Laravel dans l'existant
- pas de front séparé
- pas de starter kit ajouté tardivement pour éviter de déstabiliser le rendu

## Correspondance avec les attendus

### 1. Specs fonctionnelles et techniques

Fichiers :

- `doc/spec_ajout_jeux_utilisateur.md`
- `doc/architecture.md`
- `doc/architecture_modifications.md`
- `doc/plan_migrations_jeux_utilisateur.md`
- `doc/adr_001_jeux_utilisateur_draft_runtime.md`

### 2. Document / CDC

Fichiers :

- `doc/demarche_et_methodo.md`
- `doc/wiki_game_builder.md`
- `doc/architecture.md`

### 3. Codebase

Contenu :

- tout le dépôt

### 4. README d'installation

Fichier :

- `README.md`

### 5. Docker fonctionnel

Fichiers :

- `docker-compose.yml`
- `docker-compose.prod.yml`
- `Dockerfile`
- `docker/`

Archive de rendu complémentaire :

- `dump/wiki_builder.sql`

### 6. Front

État actuel :

- front intégré dans Laravel
- vue principale : `resources/views/welcome.blade.php`
- layout auth : `resources/views/components/layouts/app.blade.php`

### 7. Documentation API / endpoints

Sources :

- `README.md`
- `routes/api.php`
- `app/Http/Controllers/Api/ApiHomeController.php`

### 8. API

Sources :

- `routes/api.php`
- `app/Http/Controllers/Api/`
- `app/Http/Requests/`
- `app/Http/Resources/`

## Commandes de vérification

### Démarrage clé en main via Docker

```powershell
docker compose up -d --build
```

### Vérification rapide de l'API

```powershell
curl.exe http://localhost:8000/api/v1
curl.exe "http://localhost:8000/api/v1/games"
curl.exe "http://localhost:8000/api/v1/builds?game_id=1&is_public=true"
```

### Vérification de l'interface

Ouvrir :

- `http://localhost:8000`
- `http://localhost:8000/login`
- `http://localhost:8000/register`

### Vérification facultative des seeds de développement

Cette étape n'est pas requise pour le rendu minimal, mais elle peut servir si l'on veut charger des comptes et des clés API de démonstration :

```powershell
docker compose exec app php artisan db:seed --force
```

## Point d'attention environnement

Le projet demande PHP 8.4 si on l'exécute hors Docker.

Si la machine locale est en PHP 8.2, les commandes `php artisan ...` lancées hors conteneur peuvent échouer même si Docker fonctionne correctement.

Pour le rendu, la voie de référence à documenter est donc :

- exécution via Docker

## Checklist avant ZIP

- vérifier que le `README.md` est à jour
- vérifier que les documents du dossier `doc/` sont inclus
- vérifier que le snapshot BO7 dans `storage/app/private/codmunity/` est bien présent
- vérifier que Docker démarre sans étape manquante
- vérifier que l'API répond sur `/api/v1`
- vérifier que l'interface web répond sur `/`
- zipper tout le projet sous le nom attendu
