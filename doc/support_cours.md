# Support de cours — Dev Back
### M1 - DFS

---

## Sommaire

1. **Structure API** — Resource & Request
2. **Middleware** — Sécurisation : Throttle, API Key, RBAC
3. **Serialization** — Créer son propre JSON de retour structuré

---

## JWT

Le flux JWT fonctionne en 4 étapes :

1. L'utilisateur se connecte (User logs in)
2. Le serveur crée un JWT et l'envoie chiffré au client (Sends encrypted JWT)
3. Le client renvoie le JWT dans l'en-tête de chaque requête (Sends Auth request with JWT in header)
4. Le serveur compare le JWT et renvoie la réponse (Compares JWT → Sends response)

---

## 1. Structure API Avancée

### Resources & Form Requests

- **Resources** : Permettent de transformer des modèles et collections en JSON structuré
- **Form Requests** : Gèrent la validation des données entrantes et les autorisations

---

### Resources

Une classe qui transforme les modèles ou collections en format JSON structuré.

```bash
php artisan make:resource UserResource
```

**Avantages :**
- Transformation cohérente des données
- Contrôle précis sur les attributs exposés
- Inclusion conditionnelle de relations
- Formatage personnalisé des attributs
- Séparation claire entre la logique métier et la présentation des données

**Structure de base :**

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
```

**Utilisation :**

```php
// Un seul item
UserResource::make(Auth::user())

// Une collection
UserResource::collection($users)
```

**Ajouter de la data supplémentaire (méthode `with()`) :**

```php
public function with($request)
{
    return [
        'meta' => [
            'version'    => '1.0.0',
            'api_status' => 'stable',
        ],
    ];
}
```

**Résultat JSON :**

```json
{
  "data": {
    "id": 3,
    "email": "super_admin@example.com",
    "role": "super_admin",
    "created_at": "2025-02-28T08:55:50.000000Z",
    "updated_at": "2025-02-28T08:55:50.000000Z"
  },
  "meta": {
    "version": "1.0.0",
    "api_status": "stable"
  }
}
```

**Ajouter des relations ou données dans le cœur `data: {}` :**

```php
return [
    'id'    => $this->id,
    'name'  => $this->name,
    'email' => $this->email,
    // Inclusion conditionnelle
    'secret' => $this->when($request->user()->isAdmin(), 'secret-value'),
    // Relations
    'posts'  => PostResource::collection($this->whenLoaded('posts')),
];
```

---

### Requests

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // ou logique d'autorisation personnalisée
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ];
    }
}
```

**Exemple personnel avec règles avancées :**

```php
public function rules(): array
{
    return [
        'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
        'role'     => ['required', 'string', 'in:' . implode(',', RoleEnum::valuesAsArray())],
        'password' => ['required', 'string', 'max:255', 'confirmed', Password::min(8)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols()],
    ];
}
```

**Messages d'erreur personnalisés :**

```php
public function messages()
{
    return [
        'name.required'     => 'Le nom est obligatoire',
        'email.required'    => 'L\'email est obligatoire',
        'email.email'       => 'L\'email doit être une adresse valide',
        'email.unique'      => 'Cette adresse email est déjà utilisée',
        'password.required' => 'Le mot de passe est obligatoire',
        'password.min'      => 'Le mot de passe doit contenir au moins 8 caractères',
    ];
}
```

**Exemple d'utilisation dans un contrôleur :**

```php
public function store(StoreUserRequest $request)
{
    // La validation est déjà effectuée
    $user = User::create($request->validated());

    return new UserResource($user);
}
```

---

### Conclusions Resources & Requests

Les Resources et les Requests sont des outils puissants de Laravel qui permettent de :

- **Améliorer la structure de votre code** en séparant les responsabilités
- **Renforcer la sécurité** grâce à la validation et aux autorisations
- **Standardiser les réponses API** avec un formatage cohérent
- **Réduire la duplication de code** en centralisant la logique

---

## 2. Middleware — Sécurisation API

### Comment ça marche

- Un middleware est un filtre qui s'exécute **avant ou après** une requête HTTP
- Il permet de vérifier des conditions avant d'accéder à une route
- La méthode `handle()` est le point d'entrée du middleware
- `$next($request)` permet de continuer vers la prochaine étape
- Les middlewares peuvent être **globaux** ou **spécifiques** à certaines routes

**Bonnes pratiques :**
- Toujours utiliser des codes HTTP appropriés (`401` pour non autorisé)
- Valider les entrées de manière sécurisée
- Utiliser des messages d'erreur clairs
- Implémenter une gestion d'erreurs robuste
- Documenter le middleware pour les autres développeurs

**Les trois middlewares du projet :**

| Middleware | Type | Rôle |
|---|---|---|
| **Throttle** | Natif | Limite le nombre de requêtes dans un intervalle de temps |
| **CheckApiKey** | Personnalisé | Valide une clé API en en-tête des requêtes |
| **CheckRole** | Personnalisé | Gère l'accès à la route selon le rôle de l'utilisateur |

---

### Throttle

Le middleware `throttle` limite le nombre de requêtes qu'un client peut effectuer sur votre API dans un intervalle de temps donné. C'est essentiel pour :

- Prévenir les attaques par force brute
- Protéger contre les abus d'API
- Éviter la surcharge de votre serveur
- Assurer une répartition équitable des ressources

```php
// Protection contre les attaques par force brute
Route::middleware('throttle:5,1')->group(function () {
    // routes protégées
});
```

---

### Middleware ApiKey

Création d'un middleware qui inspecte l'en-tête de la requête API et interroge la base de données pour valider la clé.

```bash
php artisan make:middleware CheckApiKey
```

**Fonctionnement :**
- Vérifie la présence d'une clé API dans les en-têtes de la requête (ex : `API-KEY`)
- Valide cette clé dans la base de données
- Autorise ou refuse l'accès à la route (→ `401` si invalide)

```php
public function handle(Request $request, Closure $next): Response
{
    // Récupère la clé API depuis les en-têtes de la requête
    $apiKey = $request->header('API-KEY');

    // Vérifie si la clé existe et est valide dans la base de données
    if (!$apiKey || !ApiKey::where('key', $apiKey)->exists()) {
        // Retourne une erreur 401 si la clé est invalide
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    // Continue vers la prochaine étape si la clé est valide
    return $next($request);
}
```

> ⚠️ Bien enregistrer l'alias du middleware dans `app/Http/Kernel.php` :

```php
protected $middlewareAliases = [
    // ...
    'api.key' => \App\Http\Middleware\CheckApiKey::class,
    'role'    => \App\Http\Middleware\CheckRole::class,
];
```

---

### Middleware CheckRole

Création d'un middleware qui inspecte le rôle de l'utilisateur afin de valider ou non l'accès à la route.

```bash
php artisan make:middleware CheckRole
```

**Contexte** : l'entité `User` possède une enum qui contient les rôles.

```php
enum RoleEnum: string
{
    case ADMIN       = 'admin';
    case SUPER_ADMIN = 'super_admin';
}
```

```php
// Dans le modèle User
protected $casts = [
    'email_verified_at' => 'datetime',
    'password'          => 'hashed',
    'role'              => RoleEnum::class,
];
```

**Implémentation du middleware :**

```php
public function handle(Request $request, Closure $next, ...$roles): Response
{
    if (!$request->user() || !in_array($request->user()->role->value, $roles)) {
        return JsonApiResponse::make()->withErrors('Forbidden Access')->send(403);
    }

    return $next($request);
}
```

**Fonctionnement** : la méthode `handle()` reçoit les rôles autorisés via le paramètre variadique `...$roles`. Si l'utilisateur n'est pas connecté ou si son rôle (`->role->value`) ne figure pas dans la liste des rôles autorisés, une réponse `403` est renvoyée. Sinon, la requête continue.

---

### Utilisation des middlewares dans les routes

```php
// Middleware de rôle sur une route spécifique
Route::patch('deactivate/{id}', MediaController::class . '@deactivate')
    ->middleware(['role:admin,super_admin']);

Route::delete('delete/{id}', MediaController::class . '@delete')
    ->middleware(['role:admin,super_admin']);

// Groupe protégé par API Key
Route::group(['middleware' => ['api.key']], function () {
    Route::prefix('wp-media')->group(function () {
        Route::post('create-with-tpl',    MediaController::class . '@createTpl');
        Route::post('create-without-tpl', MediaController::class . '@createNoTpl');
        Route::get('get',                 MediaController::class . '@get');
        Route::patch('incr-counter/{id}', MediaInformationController::class . '@incrCounter');
    });
});
```

---

## 3. Serialization — JSON personnalisé

### Pourquoi standardiser les réponses API ?

La standardisation des réponses API est une bonne pratique qui permet de :

- Maintenir une cohérence dans toutes vos réponses
- Faciliter le traitement côté client
- Améliorer la maintenabilité de votre code
- Centraliser la logique de formatage

### Structure cible

```json
{
  "success": true,
  "message": "Utilisateurs récupérés avec succès",
  "data": {
    // Vos données ici
  }
}
```

La classe implémente l'interface `Responsable` de Laravel, ce qui permet de la retourner directement depuis un contrôleur.

### Exemple d'utilisation — Succès

```php
// En cas de succès
return ApiResponse::success(
    'User fetched successfully', // message
    UserResource::make(Auth::user()), // data
    200 // code de statut (200 par défaut)
);
```

**Résultat JSON :**

```json
{
  "success": true,
  "message": "User fetched successfully",
  "data": {
    "id": 3,
    "email": "super_admin@example.com",
    "role": "super_admin",
    "created_at": "2025-02-28T08:55:50.000000Z",
    "updated_at": "2025-02-28T08:55:50.000000Z"
  }
}
```

### Exemple d'utilisation — Erreur

```php
// En cas d'erreur
return ApiResponse::error(
    'User not found', // message
    null,             // data
    404               // code de statut
);
```

**Résultat JSON :**

```json
{
  "success": false,
  "message": "User not found",
  "data": null
}
```

### Surcharger les exceptions natives

Pour surcharger les exceptions natives (ex : `ValidationException` pour les Form Requests), dans `bootstrap/app.php` :

```php
withExceptions(function ($exceptions) {
    $exceptions->renderable(function (ValidationException $e) {
        // votre format de réponse personnalisé
    });
})
```

---

## 4. Laravel — Bases (Support de cours)

### Architecture MVC

Laravel suit le pattern **MVC** (Model - View - Controller) :

- **Model** : représente une table de la base de données, communique avec Eloquent/QueryBuilder
- **Controller** : effectue le code métier, fait le lien entre Model et View
- **View** : retourne la présentation (Blade pour les vues web, JSON pour les API)

---

### Model

Laravel fournit des modèles pour faciliter l'accès aux données de la base de données. Les modèles sont des classes PHP qui représentent une table de la base de données.

```php
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    /** The attributes that are mass assignable. */
    protected $fillable = [
        'firstname',
        'lastname',
        'enterprise_id',
        'is_active',
        'email',
        'password',
        'role',
    ];

    /** The attributes that should be hidden for serialization. */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** The attributes that should be cast. */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'role'              => RoleEnum::class,
        'is_active'         => 'boolean',
    ];

    public function enterprise(): BelongsTo
    {
        return $this->belongsTo(Enterprise::class);
    }
}
```

---

### Migration

Les migrations dans Laravel sont un moyen de gérer le schéma de votre base de données. Elles vous permettent de créer, modifier ou supprimer des tables, colonnes, index et autres éléments.

Les migrations sont stockées dans `database/migrations`. Chaque migration contient deux méthodes :

- `up()` : crée ou modifie les éléments de la BDD
- `down()` : supprime ou restaure les éléments de la BDD

**Exemple — table `users` :**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Authentication\Librairies\RoleEnum;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->foreignId('enterprise_id')->constrained('enterprises')->onDelete('cascade');
            $table->enum('role', RoleEnum::valuesAsArray());
            $table->boolean('is_active')->default(true);
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
```

**Exemple — table `enterprises` :**

```php
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Authentication\Librairies\PackageEnum;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enterprises', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('logo_url')->nullable();
            $table->enum('package', PackageEnum::valuesAsArray());
            $table->datetime('start_date')->nullable();
            $table->datetime('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->enum('package_n1', PackageEnum::valuesAsArray());
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enterprises');
    }
};
```

---

### Controller

Un Controller effectue le code métier et fait le lien entre le Model (BDD) et la View.

**Méthodes standards :**

| Méthode | Rôle |
|---|---|
| `index()` | Affiche une liste d'objets |
| `show()` | Affiche un objet |
| `store()` | Crée un nouvel objet |
| `update()` | Met à jour un objet existant |
| `destroy()` | Supprime un objet existant |

Il peut contenir une infinité de méthodes supplémentaires selon les besoins métiers.

**Exemple :**

```php
class AuthenticatedUserController extends Controller
{
    public function __construct(protected UserService $userService) {}

    /**
     * Show authenticated user
     * @return JsonResponse
     */
    public function show(): JsonResponse
    {
        return JsonApiResponse::make(
            UserResource::make(Auth::user())
        )->withSuccess()->send(200);
    }

    /**
     * Update authenticated user
     * @param UserRequest $request
     * @return JsonResponse
     */
    public function update(UserRequest $request): JsonResponse
    {
        $user = $this->userService->update(
            id:        Auth::user()->id,
            firstname: $request->get('firstname') ? $request->get('firstname') : null,
            lastname:  $request->get('lastname')  ? $request->get('lastname')  : null,
        );

        return JsonApiResponse::make(UserResource::make($user))
            ->withSuccess()
            ->send(200);
    }

    /**
     * Register
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->userService->create(
            email:         $request->get('email'),
            firstname:     $request->get('firstname'),
            lastname:      $request->get('lastname'),
            password:      $request->get('password'),
            role:          RoleEnum::tryFrom($request->get('role')),
            enterprise_id: $request->get('enterprise_id'),
        );

        return JsonApiResponse::make(UserResource::make($user))
            ->withSuccess()
            ->send(201);
    }
}
```

---

### Routes

Les routes définissent les points d'entrée de votre API — les URL que les clients utilisent pour accéder aux ressources.

**Routes CRUD classiques :**

| Méthode | URL | Action contrôleur | Description |
|---|---|---|---|
| `GET` | `/users` | `index()` | Liste tous les utilisateurs |
| `POST` | `/users` | `store()` | Crée un utilisateur |
| `GET` | `/users/{id}` | `show()` | Affiche un utilisateur |
| `PUT` | `/users/{id}` | `update()` | Met à jour un utilisateur |
| `DELETE` | `/users/{id}` | `destroy()` | Supprime un utilisateur |

**Exemple de fichier `api.php` :**

```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Video\app\Http\Controllers\VideoController;
use Modules\Video\app\Http\Controllers\VideoInformationController;

// Routes protégées par Sanctum
Route::prefix('videos')->middleware(['auth:sanctum'])->group(function () {
    Route::get('enterprise/{enterpriseId}/active',   VideoController::class . '@getAllActiveVideosForEnterprise');
    Route::get('enterprise/{enterpriseId}/archived', VideoController::class . '@getAllArchivedVideosForEnterprise');
    Route::get('video/{videoId}',                    VideoController::class . '@show');
    Route::patch('video/{videoId}/change-source',    VideoController::class . '@changeSourceVideo');
    Route::patch('video/{videoId}/change-title',     VideoController::class . '@changeTitleVideo');

    Route::post('video', VideoController::class . '@createVideo')
        ->middleware(['role:super_admin']);
});

// Routes protégées par API Key personnalisée
Route::prefix('player')->middleware(['verify.player.api.key'])->group(function () {
    Route::get('video/{videoId}/{hash}',             VideoController::class . '@getVideoForPlayer');
    Route::post('video/{videoId}/increment-views',   VideoInformationController::class . '@incrementViews');
});

// Routes KPI
Route::prefix('kpi')->middleware(['auth:sanctum'])->group(function () {
    Route::get('enterprise/{enterpriseId}',          VideoController::class . '@getKPIForEnterprise');
    Route::get('enterprise/{enterpriseId}/top5',     VideoController::class . '@getTop5VideosForEnterprise');
    Route::get('enterprise/{enterpriseId}/monthly-stats', VideoController::class . '@getMonthlyStatsForEnterprise');
});
```

---

## Planning des séances — Développement Back-end & conception d'API (49h)

> À l'issue de ce module, l'apprenant sera à même de proposer, justifier et mettre en place une solution back-end complète et des API performantes en réponse à des besoins métiers.

| Séance | Date |
|---|---|
| 1ère | 16/02/2026 |
| 2ème | 18/02/2026 |
| 3ème | 18/02/2026 |
| 4ème | 02/03/2026 |
| 5ème | 02/03/2026 |
| 6ème | 04/03/2026 |
| 7ème | 05/03/2026 |
| 8ème | 05/03/2026 |
| 9ème | 06/03/2026 |
| 10ème | 06/03/2026 |
| 11ème | 16/03/2026 |
| 12ème | 08/04/2026 |
| 13ème | 09/04/2026 |
| 14ème | 20/04/2026 |