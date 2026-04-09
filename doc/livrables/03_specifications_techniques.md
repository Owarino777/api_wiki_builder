# Specifications techniques

## 1. Stack technique

- **Framework** : Laravel 12
- **Langage** : PHP 8.4 (projet)
- **Serveur web** : Nginx (container `web`)
- **Runtime PHP** : container `app`
- **Base de donnees** : MySQL 8.0 (container `db`)
- **Tests** : Pest / PHPUnit
- **Orchestration locale** : Docker Compose

## 2. Architecture applicative

- `routes/api.php` : API REST versionnee
- `routes/web.php` : pages Blade + auth
- `app/Http/Controllers` : orchestration HTTP
- `app/Http/Requests` : validation entree
- `app/Http/Resources` : serialisation sortie
- `app/Services` : logique metier (rules, summary, drafts, publication)
- `app/Models` : mapping Eloquent + relations

## 3. Architecture API

## 3.1 Lecture publique

- `/api/v1/games`
- `/api/v1/items`
- `/api/v1/modifiers`
- `/api/v1/builds`
- `/api/v1/builds/compare`

## 3.2 Ecriture protegee

Middleware :

- `check.api.key`
- `require.request.user`
- `check.role:admin` (sur routes admin)

Routes critiques :

- `/api/v1/game-drafts/*`
- `/api/v1/builds` (POST/PUT/DELETE)
- `/api/v1/*` admin write endpoints

## 4. Securite

- throttle global API
- cle API requise pour ecriture
- contexte utilisateur requis pour ownership
- controles de role pour operations admin
- validations d'entree via FormRequests

## 5. Donnees et modelisation

Domaines centraux :

- runtime : `games`, `items`, `modifiers`, `builds`, pivots
- draft : `game_drafts` + sous-entites draft
- compte : `users`, `api_keys`

Point fort technique :

- service `GameDraftPublicationService` qui convertit draft -> runtime en transaction unique.

## 6. Performance et robustesse

- pagination API
- filtrage cote serveur
- serialisation resource-based
- contraintes metier centralisees dans services

## 7. Qualite et tests

Tests representatifs existants :

- tests de services build summary/rules
- tests API principaux
- tests auth/account
- test import BO7

Commande de base :

```powershell
docker compose --profile testing run --rm test
```

## 8. Deploiement local de reference

```powershell
docker compose up -d --build
```

Endpoints de controle :

- `http://localhost:8000`
- `http://localhost:8000/api/v1`
- `http://localhost:8000/up`

## 9. Decisions techniques structurantes

- separation claire des couches HTTP / validation / service / persistence
- publication draft transactionnelle pour eviter les etats incoherents
- API versionnee (`/api/v1`) pour evolutions futures
- rendu front integre Blade (pas de front separe)

