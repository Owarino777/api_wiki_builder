# Documentation d'architecture – API Quiz (Laravel)

## 1. Architecture globale

Cette API suit une architecture REST structurée selon les conventions Laravel modernes.

### Principes utilisés

- MVC strict
- Séparation des responsabilités
- Eloquent ORM
- API REST stateless
- Authentification via Laravel Sanctum
- Sécurisation via API Key + Token
- Réponses JSON standardisées
- Relations SQL propres (pivot tables)

### Structure logique

```
app
 ├── Http
 │   └── Controllers
 │
 ├── Models
 │
 ├── Enums
 │
 ├── Support
 │
database
 ├── migrations
 ├── seeders
 └── factories
```

### Objectifs

- Architecture maintenable
- Sécurité correcte
- API claire
- Reproductibilité pour d'autres projets

## 2. Gestion des rôles utilisateurs

### UserRole Enum

**Fichier :** `UserRole`

```php
enum UserRole: string
{
    case Admin = 'admin';
    case User = 'user';
}
```

### Objectif

Remplacer les strings magiques par un enum typé.

### Avantages

**Sans enum :**
```php
if($user->role === "admin")
```

**Problèmes :**
- Faute de frappe possible
- Impossible à typer
- Non maintenable

**Avec enum :**
```php
$user->role === UserRole::Admin
```

**Avantages :**
- Typage strict
- Autocomplétion IDE
- Robustesse
- Cohérence base / code

## 3. Base Controller

**Fichier :** `Controller`

```php
abstract class Controller
{
}
```

### Objectif

Classe mère pour tous les controllers.

### Importance

Laravel permet :
- Middleware commun
- Méthodes partagées
- Helpers

Même si vide, c'est un point d'extension central.

## 4. Standardisation des réponses API

### ApiResponse

**Fichier :** `ApiResponse`

#### Problème classique

**Sans standardisation :**
```php
return response()->json($data);
```

Chaque endpoint retourne une structure différente :
- `{ data: ... }`
- `{ message: ..., data: ... }`
- `{ success: true }`

Le frontend ne peut pas avoir une logique stable.

#### Solution

Créer un wrapper de réponse unique.

**Structure standardisée :**
```json
{
  "success": true,
  "message": "...",
  "data": null
}
```

#### Implémentation

```php
ApiResponse::success($message, $data, $code)
ApiResponse::error($message, $code)
```

**Exemple :**
```php
return ApiResponse::success(
    "User created",
    $data,
    201
);
```

**Avantages :**
- Structure stable et prévisible
- Facilite l'intégration frontend
- Gestion erreurs centralisée
- Meilleure lisibilité

## 5. Authentification

### AuthController

**Fichier :** `AuthController`

**Responsabilités :**
- `register` - Créer un nouvel utilisateur
- `login` - Authentifier un utilisateur
- `logout` - Terminer une session
- `me` - Récupérer l'utilisateur connecté

#### Register

**Processus :**
```php
User::create()
$user->createToken()
```

**Token utilisé :** Laravel Sanctum `plainTextToken`

**Utilisation dans les requêtes :**
```
Authorization: Bearer TOKEN
```

#### Login

**Processus :**
1. Validation de la requête
2. `Auth::attempt()` - Vérification credentials
3. Génération du token
4. Retour user + token

#### Logout

```php
$request->user()->currentAccessToken()->delete()
```

Supprime le token actif de la session.

#### Me

Permet au frontend de récupérer l'utilisateur connecté avec ses permissions.

#### Pourquoi Sanctum

**Avantages :**
- Simple à mettre en place
- Sécurisé par défaut
- Compatible SPA / mobile
- Gestion multi tokens native

## 6. Gestion des Quiz

### QuizController

**Fichier :** `QuizController`

**Endpoints CRUD :**
- `index` - Lister tous les quiz
- `show` - Détail d'un quiz
- `store` - Créer un quiz
- `update` - Modifier un quiz
- `destroy` - Supprimer un quiz

#### index()

Charge les relations :
```php
Quiz::with(['theme', 'questions'])
```

**Intérêt du eager loading :**

**Sans eager loading (N+1 problem) :**
- 1 query quizzes
- + N queries themes
- + N queries questions

**Avec eager loading :**
- 1 query quizzes
- 1 query themes
- 1 query questions

La performance est bien meilleure.

#### show()

Charge toutes les relations :
- `theme` - Le thème du quiz
- `questions` - Les questions du quiz
- `users` - Les participants

Permet de voir les participants et leurs scores.

#### store()

Création d'un quiz avec deux modes possibles :

**Mode 1 - Manuel :**
- Paramètre `question_ids`
- Fournir directement les IDs des questions

**Mode 2 - Automatique :**
- Paramètre `selection_mode = theme`
- Paramètre `questions_count`
- Le controller sélectionne les questions aléatoirement

**Fonction utilitaire :** `resolveQuestionIds()`

#### resolveQuestionIds()

Résout les IDs des questions selon le mode.

**Cas gérés :**
- `manual` - Utilise les IDs fournis
- `theme` - Sélectionne aléatoirement par thème

**Algorithme :**
```php
Question::query()
    ->where('theme_id', $themeId)
    ->inRandomOrder()
    ->limit($count)
```

**Utilité :**
- Générer des quiz dynamiques
- Varier les questions à chaque création

## 7. Gestion des utilisateurs

### UserController

**Fichier :** `UserController`

**Endpoints :**
- `index` - Lister les utilisateurs
- `show` - Détail utilisateur
- `store` - Créer un utilisateur
- `participateInQuiz` - Ajouter participation
- `updateQuizScore` - Mettre à jour le score

#### participateInQuiz()

Gère la relation many-to-many `user <-> quiz` via pivot table.

**Insertion :**
- `score = 0` - Score initial
- `participated_at = now()` - Date participation

#### updateQuizScore()

Met à jour le score dans la pivot table.

**Utilise :** `updateExistingPivot()`

```php
$user->quizzes()->updateExistingPivot($quizId, ['score' => $score])
```

## 8. Modèles Eloquent

### User

**Fichier :** `User`

**Traits utilisés :**
- `HasApiTokens` - Pour Sanctum
- `HasFactory` - Pour les tests
- `Notifiable` - Pour les notifications

#### Casts

```php
'password' => 'hashed'        // Hash auto
'role' => UserRole::class     // Conversion enum
```

**Avantages :**
- Password hashé automatiquement
- Rôle converti en enum

#### Relation

`users <-> quizzes` via pivot table avec :
- `score` - Score au quiz
- `participated_at` - Date participation

### Quiz

**Fichier :** `Quiz`

**Relations :**
- `Quiz -> Theme` (belongsTo)
- `Quiz -> Questions` (belongsToMany via pivot)
- `Quiz -> Users` (belongsToMany via pivot)

#### Pivot tables

**question_quiz :**
- Relation many-to-many
- Contrainte unique : `(question_id, quiz_id)`

**quiz_user :**
- Relation many-to-many
- Stockage du score et date participation

### ApiKey Model

**Fichier :** `ApiKey`

**But :** Ajouter une sécurité API supplémentaire par clé machine.

**Structure :**
- `name` - Nom descriptif
- `key` - La clé secrète
- `is_active` - Flag activation

**Utilisation :** Middleware `CheckApiKey`

## 9. Rate Limiting

### AppServiceProvider

**Fichier :** `AppServiceProvider`

#### Configuration

```php
RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)
        ->by($request->user()?->id ?: $request->ip());
});
```

**Limites :**
- 60 requêtes par minute
- Clé : `user_id` si connecté, sinon `ip`

#### Utilité

- Prévenir l'abus API
- Protection légère contre les DDOS
- Protection contre le brute force

## 10. Base de données

### Table users

**Migration :** `0001_01_01_000000_create_users_…`

**Champs :**
- `id` - Clé primaire
- `username` - Nom d'utilisateur unique
- `email` - Email unique
- `password` - Mot de passe
- `role` - Rôle (admin/user)
- `remember_token` - Token souvenance
- `timestamps` - created_at, updated_at

### Table quizzes

**Migration :** `2026_02_18_000040_create_quizze…`

**Champs :**
- `id` - Clé primaire
- `title` - Titre du quiz
- `description` - Description
- `theme_id` - Clé étrangère vers themes
- `timestamps`

### Table questions

**Migration :** `2026_02_18_000030_create_questio…`

**Champs :**
- `id` - Clé primaire
- `theme_id` - Clé étrangère
- `title` - Énoncé
- `proposals` - JSON array
- `responses` - JSON array
- `timestamps`

### Pivot question_quiz

**Migration :** `2026_02_18_000050_create_questi…`

**Relation :** `questions <-> quizzes`

**Clé unique :** `(question_id, quiz_id)` - Empêche les doublons

### Pivot quiz_user

**Migration :** `2026_02_18_000060_create_quiz_u…`

**Relation :** `users <-> quizzes`

**Champs de pivot :**
- `score` - Score de l'utilisateur
- `participated_at` - Date participation

### Table api_keys

**Migration :** `2026_03_02_000010_create_api_ke…`

**But :** Authentification machine-to-machine

**Champs :**
- `name` - Clé machine
- `key` - Clé secrète
- `is_active` - Statut activation

### Migration ajout du rôle

**Migration :** `2026_03_02_000020_add_role_to_u…`

Ajoute la colonne `role` à la table `users` avec valeur par défaut `user`.

**Importance :** Permet le RBAC (Role Based Access Control)

## 11. Seeders

### DatabaseSeeder

**Fichier :** `DatabaseSeeder`

**Crée :**
- `admin@example.com` - Compte administrateur
- `test@example.com` - Compte test

**Lance ensuite :**
- `ThemeSeeder` - Données de thèmes
- `QuestionSeeder` - Données de questions
- `ApiKeySeeder` - Clés API de test

### ApiKeySeeder

**Fichier :** `ApiKeySeeder`

**Crée les clés :**
- `default-key` - Clé par défaut
- `mobile-app` - Clé pour app mobile
- `revoked-key` - Clé révoquée (tests)

**Utilité :** Tests des endpoints API

## 12. Factory

### UserFactory

**Fichier :** `UserFactory`

**Génère :** Faux utilisateurs pour tests

**Utile pour :**
- Tests unitaires
- Seed de développement
- Données de test

## 13. Routes API

**Fichier :** `api.php`

#### Structure

```
- Auth routes (publiques)
  - POST /auth/register
  - POST /auth/login
  
- API routes (protégées)
  - middleware: api_key, auth:sanctum
  - GET / POST (lecture)
  
- Admin routes (admin only)
  - middleware: role:admin
  - PUT / DELETE (écriture)
```

#### Middleware appliqués

- `api_key` - Vérifier clé API
- `auth:sanctum` - Authentification token
- `role:admin,user` - RBAC

#### Bonnes pratiques

- Séparation lecture / écriture
- RBAC clair et centralisé

## 14. Routes web

**Fichier :** `web.php`

Route par défaut Laravel.

Non utilisé pour cette API REST.

## 15. Middleware de sécurité

Les middlewares exécutent du code avant ou après une requête HTTP.

**Rôles :**
- Sécuriser l'API
- Normaliser les réponses
- Tracer les requêtes
- Appliquer les règles d'accès

### CheckApiKey

**Fichier :** `CheckApiKey`

#### Objectif

Vérifier qu'une requête possède une clé API valide.

**Header requis :**
```
API-KEY: votre_cle
```

#### Fonctionnement

1. Récupération de la clé depuis le header
2. Vérification présence de la clé
3. Vérification en base de données
4. Refus si invalide : `401 Unauthorized`

**Code :**
```php
ApiKey::where('key', $apiKey)
      ->where('is_active', true)
      ->exists()
```

#### Utilité

- Limiter l'accès API
- Identifier les clients
- Bloquer certaines applications

**Architecture sécurité complète :**
```
Client
  ↓
API Key Middleware
  ↓
Sanctum Auth
  ↓
Role Middleware
  ↓
Controller
```

### CheckRole

**Fichier :** `CheckRole`

#### Objectif

Contrôler le rôle de l'utilisateur.

**Utilisation dans les routes :**
```php
->middleware('role:admin')
->middleware('role:admin,user')
```

#### Fonctionnement

1. Récupération utilisateur authentifié
2. Conversion des rôles requis en Enum
3. Comparaison avec le rôle utilisateur
4. Refus si absent : `403 Access denied`

**Code :**
```php
$user = $request->user();
$role = UserRole::from($role);
```

#### Avantages

**Utilise :** Enum + Middleware

**Versus :** `if($user->role === 'admin')`

**Bénéfices :**
- Typage strict
- Sécurité renforcée
- Centralisation des règles d'accès

### ForceJsonResponse

**Fichier :** `ForceJsonResponse`

#### Problème

Laravel retourne parfois des pages HTML pour les erreurs (404, 500, etc.).

Cela casse une API qui attend du JSON.

#### Solution

Forcer le header Accept sur toutes les requêtes :
```php
$request->headers->set('Accept', 'application/json');
```

#### Résultat

Toutes les erreurs deviennent JSON :
```json
{
  "success": false,
  "message": "Not Found"
}
```

### LogApiRequest

**Fichier :** `LogApiRequest`

#### Objectif

Tracer toutes les requêtes API pour monitoring et debug.

**Format log :**
```
method: POST
url: /api/quizzes
status: 200
duration: 32ms
ip: 127.0.0.1
user_id: 4
```

#### Fonctionnement

1. Enregistrer le temps de départ : `microtime(true)`
2. Exécuter la requête
3. Calculer la durée
4. Écrire le log : `Log::info(...)`

#### Importance

**Permet :**
- Debug efficace
- Monitoring performances
- Détection abusive API
- Audit sécurité

## 16. Validation centralisée

Laravel utilise des FormRequest pour valider les entrées avant le controller.

**Avantages :**
- Validation hors controller
- Code clean et maintenable
- Gestion erreurs automatique

### ApiFormRequest

**Fichier :** `ApiFormRequest`

Classe abstraite de base pour toutes les validations.

#### Fonction principale

Personnaliser le format des erreurs de validation.

**Par défaut :** Laravel retourne du HTML

**Implémentation :** JSON standardisé

**Code :**
```php
throw new HttpResponseException(
    ApiResponse::error($validator->errors()->toArray(), 422)
);
```

#### Resultat

Erreur JSON cohérente :
```json
{
  "success": false,
  "message": {
     "email": ["The email field is required"]
  }
}
```

### LoginRequest

**Fichier :** `LoginRequest`

**Validation login :**
- `email` : required, valid email
- `password` : required, min 8 caractères

### RegisterRequest

**Fichier :** `RegisterRequest`

**Validation inscription :**
- `username` : optional
- `email` : required, unique
- `password` : required, min 8

### StoreQuestionRequest

**Fichier :** `StoreQuestionRequest`

**Structure question :**
- `theme_id` : required
- `title` : required
- `proposals[]` : array, min 2
- `responses[]` : array, min 1

#### Validation avancée

```php
array_diff($responses, $proposals)
```

Vérifie que chaque réponse est dans les propositions.

### StoreQuizRequest

**Fichier :** `StoreQuizRequest`

Gère les modes de création de quiz.

**Modes supportés :**
- `theme` - Sélection par thème
- `random` - Sélection aléatoire
- `manual` - IDs fournis manuellement

**Validation conditionnelle :**
```php
'theme_id' => 'required_if:mode,theme'
'question_ids' => 'required_if:mode,manual'
```

**Validation avancée :** Vérifier la disponibilité réelle des questions

### UpdateQuestionRequest

**Fichier :** `UpdateQuestionRequest`

**Validation PATCH partiel :**
- Utilise `sometimes|required`
- Permet la modification partielle

### UpdateQuizRequest

**Fichier :** `UpdateQuizRequest`

**Validation simple :**
- `title` : optional
- `description` : optional
- `theme_id` : optional

### StoreThemeRequest

**Fichier :** `StoreThemeRequest`

**Validation :**
- `name` : required, unique
- `description` : optional

### StoreUserRequest

**Fichier :** `StoreUserRequest`

**Validation :**
- `username` : required, unique
- `email` : required, unique
- `password` : required, min 8

## 17. API Resources

Les Resources contrôlent la structure JSON retournée.

**Sans Resource :**
```php
return $model;
```

Laravel retourne toutes les colonnes.

**Problèmes :**
- Fuite données sensibles
- Structure incohérente
- Trop dépendant du modèle

### UserResource

**Fichier :** `UserResource`

**Colonnes exposées :**
- `id`
- `username`
- `email`
- `role`
- `created_at`
- `updated_at`

**Masquées :**
- `password`
- `remember_token`

### QuizResource

**Fichier :** `QuizResource`

**Retourne :**
- `id`, `title`, `description`
- `theme` (avec whenLoaded)
- `questions` (avec whenLoaded)
- `users` (avec whenLoaded)

**Optimisation :** `whenLoaded()` charge relations seulement si demandées

### QuestionResource

**Fichier :** `QuestionResource`

**Structure :**
- `id`, `theme_id`, `title`
- `proposals` (array)
- `responses` (array)
- `theme` (relation)
- `quizzes` (relation)

### ThemeResource

**Fichier :** `ThemeResource`

**Structure :**
- `id`, `name`, `description`
- `questions` (collection)

### ParticipationResource

**Fichier :** `ParticipationResource`

**Retour des données pivot :**
- `user_id`
- `quiz_id`
- `score`
- `participated_at`

**Utilisé pour :**
- Participations à un quiz
- Affichage des scores

## 18. Pipeline de sécurité complet

Flux complet d'une requête sécurisée :

```
Client
  ↓
API Key Middleware
  ↓
Force JSON Response Middleware
  ↓
Sanctum Authentication
  ↓
Role Authorization Middleware
  ↓
Form Request Validation
  ↓
Controller (Orchestration)
  ↓
Resource Transformation
  ↓
ApiResponse Wrapper
  ↓
JSON Response
  ↓
Log API Request
  ↓
Client
```

## 19. Authentification : Sanctum vs JWT

### Contexte

Le support pédagogique mentionne JWT. Ce projet utilise **Sanctum**, qui offre une approche similaire mais différente.

### JWT (Json Web Token)

- Token **auto-contenu** avec claims et signature
- Stateless côté serveur
- Validation par signature cryptographique

### Sanctum

- Token **stocké côté serveur** (table personal_access_tokens)
- Stateful côté serveur
- Validation par existence/présence du token

### Flux client (identique)

**JWT et Sanctum :**
```
Authorization: Bearer <TOKEN>
```

### Flux serveur (différent)

**JWT :**
```
Serveur valide la signature
Serveur lit les claims
```

**Sanctum :**
```
Serveur cherche le token en BDD
Serveur vérifie qu'il est valide/actif
```

### Justification du choix

Sanctum pour ce projet car :
- Intégration native Laravel
- Gestion tokens plus simple
- Révocation immédiate possible
- Ideal pour SPA + API

**AuthController montre exactement ce flux :**
```
Register/Login → createToken() → Bearer Token
```

## 20. Headers obligatoires (checklist client)

Requête complète à un endpoint protégé :

```http
POST /api/quizzes
API-KEY: your-api-key-here
Authorization: Bearer sanctum_token_here
Accept: application/json
Content-Type: application/json

{
  "title": "Quiz Test",
  "description": "...",
  "theme_id": 1
}
```

**Headers requis :**
- `API-KEY` - Clé machine (CheckApiKey middleware)
- `Authorization: Bearer ...` - Token utilisateur (Sanctum)
- `Accept: application/json` - Force réponse JSON

## 21. Alignement pédagogique

### Support "Dev Back — M1 DFS"

Cette architecture implémente :

✅ **Resources & Form Requests :**
- Resources pour transformation JSON
- Form Requests avec validation avancée
- Séparation Controller/Request/Resource

✅ **Middleware de sécurité :**
- API Key (CheckApiKey)
- RBAC typé (CheckRole + Enum)
- Rate Limiting (RateLimiter)

✅ **Sérialisation standardisée :**
- Structure `success/message/data`
- Erreurs JSON cohérentes
- Réponses stables pour frontend

✅ **Architecture MVC pour API :**
- Models avec relations
- Controllers orchestration
- Resources comme "Views"

✅ **Routes protégées :**
- Groupes par permission
- Middleware chaîné
- Séparation publique/authentifiée/admin
