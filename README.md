# Wiki Builder API (Laravel 12)

API REST pour le projet Wiki Builder — gestion d'un catalogue multi-jeux d'équipements et d'un module Builder pour assembler et comparer des configurations ("builds/loadouts").

Le seed principal livre un vrai catalogue **Call of Duty: Black Ops III** normalisé pour l'application, plus un second jeu synthétique pour démontrer le multi-jeux.

## Stack technique

- **Laravel 12** — Framework PHP
- **PHP 8.3+**
- **MySQL 8.0**
- **Docker Compose** (app + db)

---

## Déploiement rapide (Docker)

```bash
# Cloner le projet
git clone <url-du-repo>
cd api_wiki_builder

# Copier le fichier d'environnement
cp .env.example .env

# Lancer les conteneurs
docker compose up -d --build
```

L'API démarre automatiquement sur `http://localhost:8000/api/v1`.

Le docker-compose exécute automatiquement :
1. `composer install`
2. `php artisan key:generate`
3. `php artisan migrate:fresh --seed --force`
4. `php artisan serve`

### Dump SQL

Un dump SQL est fourni dans `dump/wiki_builder.sql` pour importer les données sans passer par les migrations :

```bash
mysql -u wiki -pwiki wiki_builder < dump/wiki_builder.sql
```

---

## Déploiement local (sans Docker)

### Prérequis

- PHP 8.3+
- Composer 2.x
- MySQL 8.0

### Installation

```bash
composer install
cp .env.example .env
```

Configurer `.env` :
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wiki_builder
DB_USERNAME=root
DB_PASSWORD=
```

```bash
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan migrate:fresh --seed --force
php artisan serve
```

L'API est accessible sur `http://localhost:8000/api/v1`.
Une vitrine front légère est aussi disponible sur `http://localhost:8000/`.

---

## Structure du projet

```
app/
├── Enums/
│   └── UserRole.php              # Enum PHP pour les rôles (admin/user)
├── Http/
│   ├── Controllers/Api/          # 9 controllers REST
│   ├── Middleware/                # CheckApiKey, CheckRole
│   ├── Requests/                 # 10 Form Requests (validation)
│   └── Resources/                # 6 API Resources (sérialisation JSON)
├── Models/                       # 8 modèles Eloquent
├── Services/                     # BuildRuleService, BuildSummaryService
├── Support/
│   └── ApiResponse.php           # Wrapper réponses JSON standardisées
database/
├── migrations/                   # 12 fichiers (1 par table)
├── seeders/                      # WikiBuilderSeeder + DatabaseSeeder
dump/
└── wiki_builder.sql              # Dump SQL avec données de test
doc/
├── AI_INSTRUCTIONS.md            # Cahier des charges IA
├── wiki_game_builder.md          # Spécifications fonctionnelles + MPD
├── architecture.md               # Documentation architecture prof
├── support_cours.md              # Support de cours du prof
└── demarche_et_methodo.md        # Démarches de construction
```

---

## Sécurité

| Couche | Middleware | Description |
|--------|-----------|-------------|
| Throttle | `throttle:60,1` | 60 requêtes/min sur toutes les routes |
| API Key | `check.api.key` | Header `API-KEY` validé en base (`api_keys.is_active = true`) |
| Rôle | `check.role:admin` | Header `X-USER-ID` → vérifie `users.role` en base |

Les middlewares sont enregistrés dans `bootstrap/app.php` (convention Laravel 12, pas `Kernel.php`).

---

## RGPD

L'API implémente les droits RGPD suivants (routes protégées admin) :

| Droit | Endpoint | Description |
|-------|----------|-------------|
| Droit d'accès | `GET /api/v1/users/{id}/export` | Exporte toutes les données personnelles |
| Droit à l'oubli | `POST /api/v1/users/{id}/anonymize` | Anonymise username et email |
| Droit de suppression | `DELETE /api/v1/users/{id}` | Supprime l'utilisateur, dissocie les builds |

**Principes RGPD respectés :**
- Minimisation des données : pas de mot de passe stocké, données strictement nécessaires
- Droit d'accès : endpoint d'export des données personnelles
- Droit à l'effacement : anonymisation ou suppression complète
- Pas de données sensibles collectées (pas de données de santé, biométriques, etc.)

---

## Endpoints API

### Base URL : `/api/v1`

### Lecture publique (GET)

| Endpoint | Description |
|----------|-------------|
| `GET /games` | Liste des jeux (paginé) |
| `GET /games/{id}` | Détail d'un jeu |
| `GET /games/{id}/build-profiles` | Profils d'un jeu |
| `GET /games/{id}/equipment-items` | Équipements d'un jeu (filtres + tri + pagination) |
| `GET /games/{id}/equipment-modifiers` | Modificateurs d'un jeu |
| `GET /tags` | Liste des tags avec équipements associés |
| `GET /equipment-items` | Liste globale des équipements |
| `GET /equipment-items/{id}` | Détail d'un équipement |
| `GET /equipment-modifiers` | Liste globale des modificateurs |
| `GET /equipment-modifiers/{id}` | Détail d'un modificateur avec équipements compatibles |
| `GET /builds/{id}` | Détail d'un build |
| `GET /builds/{id}/summary` | Résumé stats d'un build |
| `POST /builds/compare` | Comparaison de deux builds |

### Écriture protégée (API-KEY)

| Endpoint | Description |
|----------|-------------|
| `POST /builds` | Créer un build |
| `PUT /builds/{id}` | Modifier un build |
| `DELETE /builds/{id}` | Supprimer un build |

### Administration (API-KEY + X-USER-ID admin)

| Endpoint | Description |
|----------|-------------|
| `POST /build-profiles` | Créer un profil |
| `PUT /build-profiles/{id}` | Modifier un profil |
| `DELETE /build-profiles/{id}` | Supprimer un profil |
| `POST /equipment-items` | Créer un équipement |
| `PUT /equipment-items/{id}` | Modifier un équipement |
| `DELETE /equipment-items/{id}` | Supprimer un équipement |
| `POST /equipment-modifiers` | Créer un modificateur |
| `PUT /equipment-modifiers/{id}` | Modifier un modificateur |
| `DELETE /equipment-modifiers/{id}` | Supprimer un modificateur |
| `POST /equipment-items/{id}/compatible-modifiers/{modifierId}` | Ajouter compatibilité |
| `DELETE /equipment-items/{id}/compatible-modifiers/{modifierId}` | Retirer compatibilité |
| `POST /equipment-items/{id}/tags/{tagId}` | Associer un tag |
| `DELETE /equipment-items/{id}/tags/{tagId}` | Dissocier un tag |
| `GET /users/{id}/export` | Export données RGPD |
| `POST /users/{id}/anonymize` | Anonymisation RGPD |
| `DELETE /users/{id}` | Suppression RGPD |

### Filtres & tri disponibles

**Equipment items** : `game_id`, `slot`, `rarity`, `tier_level`, `score_min`, `score_max`, `tag`, `sort_by`, `sort_dir`, `per_page`

**Equipment modifiers** : `game_id`, `modifier_type`, `score_min`, `score_max`, `sort_by`, `sort_dir`, `per_page`

---

## Format des réponses (Serialization)

Toutes les réponses suivent le format `ApiResponse` standardisé :

```json
{
  "success": true,
  "message": "OK",
  "data": { ... }
}
```

En cas d'erreur :
```json
{
  "success": false,
  "message": "Validation failed.",
  "errors": { ... }
}
```

La gestion des exceptions est centralisée dans `bootstrap/app.php` : les erreurs 404, 429 (throttle) et les exceptions non gérées retournent toujours le format JSON standardisé.

`GET /builds/{id}/summary` et `POST /builds/compare` utilisent désormais le même envelope JSON que le reste de l'API.

---

## Règles métier

1. **Build mono-jeu** : un build ne peut référencer que des items/modifiers/profils du même jeu
2. **Compatibilité** : un modifier ne peut être ajouté que si la compatibilité existe dans `equipment_item_compatible_modifiers`
3. **Résumé (NULL = 0)** : totaux = SUM(stats items) + SUM(deltas modifiers), avec `COALESCE(..., 0)`

---

## Identifiants de test (seeder)

| Donnée | Valeur |
|--------|--------|
| API-KEY | `dev-wiki-builder-key-1234567890abcdef` |
| Admin (X-USER-ID) | `1` (role=admin) |
| User standard | `2` (role=user) |

Le jeu `1` correspond à `Call of Duty: Black Ops III`.

---

## Exemples curl

### Lire les jeux

```bash
curl -s "http://localhost:8000/api/v1/games"
```

### Lire les équipements avec filtres

```bash
curl -s "http://localhost:8000/api/v1/games/1/equipment-items?slot=weapon&tier_level=L1&rarity=standard-issue&score_min=40&sort_by=score&sort_dir=desc&per_page=10"
```

### Lire les tags avec leurs équipements associés

```bash
curl -s "http://localhost:8000/api/v1/tags"
```

### Lire un modificateur avec ses équipements compatibles

```bash
curl -s "http://localhost:8000/api/v1/equipment-modifiers/1"
```

### Créer un build (API key requise)

```bash
curl -s -X POST "http://localhost:8000/api/v1/builds" \
  -H "Content-Type: application/json" \
  -H "API-KEY: dev-wiki-builder-key-1234567890abcdef" \
  -d '{
    "game_id": 1,
    "user_id": 2,
    "build_profile_id": 1,
    "title": "My Build",
    "equipment_items": [
      { "equipment_item_id": 1, "position": 1 },
      { "equipment_item_id": 2, "position": 2 }
    ],
    "equipment_modifiers": [
      { "equipment_item_id": 1, "equipment_modifier_id": 1 }
    ]
  }'
```

### Résumé d'un build

```bash
curl -s "http://localhost:8000/api/v1/builds/1/summary"
```

Exemple de structure :

```json
{
  "success": true,
  "message": "OK",
  "data": {
    "build_id": 1,
    "items_count": 3,
    "modifiers_count": 3,
    "totals": {
      "score": 0,
      "attack": 0,
      "defense": 0,
      "utility": 0,
      "accuracy": 0,
      "mobility": 0
    }
  }
}
```

### Comparer deux builds

```bash
curl -s -X POST "http://localhost:8000/api/v1/builds/compare" \
  -H "Content-Type: application/json" \
  -d '{"left_build_id": 1, "right_build_id": 2}'
```

## Données BO3

- Les noms d'armes et d'accessoires BO3 sont seedés avec des `source.page_url` stockés dans `metadata`.
- Les URLs d'image ont été conservées uniquement quand elles ont pu être vérifiées proprement pendant la préparation du seed.
- Les champs `score`, `attack`, `utility`, `accuracy`, `mobility` restent des scores de builder simplifiés, pas une copie brute de statistiques internes du jeu.

### Endpoint admin (API key + rôle admin)

```bash
curl -s -X POST "http://localhost:8000/api/v1/equipment-items" \
  -H "Content-Type: application/json" \
  -H "API-KEY: dev-wiki-builder-key-1234567890abcdef" \
  -H "X-USER-ID: 1" \
  -d '{
    "game_id": 1,
    "name": "New Helmet",
    "slot": "helmet",
    "rarity": "rare",
    "score": 55
  }'
```

### RGPD — Export données utilisateur

```bash
curl -s "http://localhost:8000/api/v1/users/2/export" \
  -H "API-KEY: dev-wiki-builder-key-1234567890abcdef" \
  -H "X-USER-ID: 1"
```

### RGPD — Anonymisation

```bash
curl -s -X POST "http://localhost:8000/api/v1/users/2/anonymize" \
  -H "API-KEY: dev-wiki-builder-key-1234567890abcdef" \
  -H "X-USER-ID: 1"
```

---

## Commandes utiles

```bash
# Commandes exigées par le cahier des charges
docker compose up -d --build
php artisan migrate --force
php artisan db:seed --force

# Reset complet
php artisan migrate:fresh --seed --force

# Liste des routes
php artisan route:list

# Lancer les tests
php artisan test
```
