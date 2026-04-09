# Plan de validation

## 1. Objectif

Verifier que les livrables fonctionnels/techniques sont conformes et demonstrables.

## 2. Strategie

- Validation demarrage Docker
- Validation routes API
- Validation parcours web critiques
- Validation securite (api key, role, ownership)
- Validation publication draft transactionnelle

## 3. Pre-requis

```powershell
docker compose up -d --build
```

## 4. Jeux de tests manuels (web)

### TV-01 - Auth

- ouvrir `/register`, creer compte
- ouvrir `/login`, se connecter
- verifier acces `/dashboard` et `/studio/games/create`

### TV-02 - Builder

- ouvrir `/`
- choisir un jeu
- ajouter au moins 2 items
- verifier affichage matrice

### TV-03 - Studio

- creer un draft
- remplir fondation
- ajouter categories/items/modifiers
- lancer publication

### TV-04 - Compte RGPD

- ouvrir `/account`
- tester export
- tester anonymisation (password)

## 5. Jeux de tests API (curl/Postman)

### TA-01 - API home

```powershell
curl.exe http://localhost:8000/api/v1
```

### TA-02 - Lecture publique

```powershell
curl.exe "http://localhost:8000/api/v1/games"
curl.exe "http://localhost:8000/api/v1/items?per_page=5"
curl.exe "http://localhost:8000/api/v1/builds?per_page=5"
```

### TA-03 - Compare builds

```powershell
curl.exe -X POST http://localhost:8000/api/v1/builds/compare ^
  -H "Content-Type: application/json" ^
  -d "{\"left_build_id\":1,\"right_build_id\":2}"
```

### TA-04 - Drafts proteges (avec API-KEY)

```powershell
curl.exe -X GET http://localhost:8000/api/v1/game-drafts ^
  -H "API-KEY: <api-key>"
```

Attendu :

- 401/403 sans contexte user requis
- 200 avec contexte valide

## 6. Verification securite

- ecriture API impossible sans `API-KEY`
- routes admin write impossibles sans role admin
- acces draft limite a son owner (sauf admin + all)

## 7. Verification transaction publication

Cas attendu :

- si validation draft echoue -> pas de jeu runtime cree
- si validation draft reussit -> jeu cree + draft passe en `published`

## 8. Trace de commande utile

```powershell
docker compose exec app php artisan route:list --path=api
docker compose --profile testing run --rm test
```

## 9. Critere de go rendu

- stack demarre sans etape manuelle non documentee
- routes API repondent
- parcours web principal exploitable
- dossier `doc/livrables` complet
- UML `.puml` fournis

