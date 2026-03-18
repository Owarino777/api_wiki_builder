# Rendu Final Prof

## Objectif

Ce document sert de checklist de rendu pour le zip final demande dans Teams.

Nom attendu :

- `<nom_prenom.zip>`

Le zip doit contenir le projet complet avec son dossier `doc`, le code, le `README`, Docker et le dump SQL.

## Version Laravel

Etat actuel du projet :

- application basee sur Laravel 12
- dependance declaree : `laravel/framework ^12.0`
- PHP requis par le projet : `^8.4`

Point de veille documentation :

- la documentation officielle Laravel 13.x est disponible
- le projet n'a pas besoin de migrer vers Laravel 13 pour le rendu
- si un switch est envisage plus tard, il faudra documenter le choix et suivre l'upgrade guide officiel

Sources officielles consultees :

- Laravel 12 Installation : https://laravel.com/docs/12.x/installation
- Laravel 12 Configuration : https://laravel.com/docs/12.x/configuration
- Laravel 13 Configuration : https://laravel.com/docs/13.x/configuration
- Laravel Starter Kits : https://laravel.com/docs/13.x/starter-kits

Decision recommandee pour le rendu :

- conserver Laravel 12 pour stabilite
- documenter que Laravel 13 existe
- ne migrer que si un besoin fonctionnel ou pedagogique le justifie

## Starter Kit / Auth

Pour une vraie IHM utilisateur avec :

- inscription
- connexion / deconnexion
- espace utilisateur
- creation de jeux
- creation d'equipements
- creation et comparaison de builds

la direction recommandee est :

- Laravel full-stack
- starter kit Livewire
- pas de front separe

Justification :

- Livewire reste dans l'ecosysteme Laravel / Blade / PHP
- il est adapte aux parcours wizard et formulaires metier
- il est plus simple a integrer qu'une SPA React / Vue pour ce projet

## Correspondance avec les attendus du prof

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

- tout le depot

### 4. README d'installation

Fichier :

- `README.md`

### 5. Docker fonctionnel avec dump SQL

Fichiers :

- `docker-compose.yml`
- `docker-compose.prod.yml`
- `Dockerfile`
- `docker/`
- `dump/wiki_builder.sql`

### 6. Front si il y en a un

Etat actuel :

- front de demo integre dans Laravel
- vue principale : `resources/views/welcome.blade.php`

### 7. Documentation API / Endpoint

Sources :

- `README.md`
- `routes/api.php`
- `app/Http/Controllers/Api/ApiHomeController.php`

### 8. L'API

Sources :

- `routes/api.php`
- `app/Http/Controllers/Api/`
- `app/Http/Requests/`
- `app/Http/Resources/`

## Commandes de verification

### Demarrage cle en main via Docker

```powershell
Copy-Item .env.example .env
docker compose up -d --build
```

### Seed complet de demonstration

```powershell
docker compose exec app php artisan db:seed --force
```

### Verification rapide API

```powershell
curl.exe http://localhost:8000/api/v1
curl.exe "http://localhost:8000/api/v1/games"
curl.exe "http://localhost:8000/api/v1/builds?game_id=1&is_public=true"
```

### Verification interface

Ouvrir :

- `http://localhost:8000`

## Point d'attention environnement

Le projet demande PHP 8.4 si tu l'executes hors Docker.

Si la machine locale est en PHP 8.2, les commandes `php artisan ...` et `vendor/bin/pest` lancees hors conteneur peuvent echouer meme si Docker fonctionne correctement.

Pour le rendu, la voie de reference a documenter est donc :

- execution via Docker

## Checklist avant zip

- verifier que le `README.md` est a jour
- verifier que `dump/wiki_builder.sql` est present
- verifier que les documents du dossier `doc/` sont bien inclus
- verifier que Docker demarre sans etape manquante
- verifier que l'API repond sur `/api/v1`
- verifier que l'interface web repond sur `/`
- zipper tout le projet sous le nom attendu
