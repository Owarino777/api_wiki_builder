# 1. Resume executif

La feature recommandee n'est pas un simple `POST /games`. Le bon design pour ton projet est un parcours en deux couches :

- une couche `draft` dediee a la creation guidee et incomplete ;
- une couche `published` qui alimente uniquement les tables runtime deja utilisees par le builder (`games`, `archetypes`, `items`, `item_slots`, `modifiers`, `categories`, pivots).

Cette separation est le point cle pour empecher un utilisateur de polluer l'API publique avec un jeu incoherent, incomplet ou structurellement faux.

La recommandation est donc :

- garder `games` comme source de verite des jeux utilisables par l'application ;
- introduire un aggregate de brouillon (`game_drafts` + sous-entites de draft) ;
- piloter la creation par template de categorie (`fps`, `rpg`) versionne et defini cote backend ;
- autoriser les sauvegardes partielles ;
- interdire la publication tant que les regles structurelles ne sont pas satisfaites ;
- publier via une transaction unique qui convertit le draft en dataset runtime propre.

Cette approche est la plus adaptee a ton architecture actuelle, au bareme du prof, et a la logique backend-first.

Elle renforce directement :

- `Fonctionnel` : parcours exploitable et reprise de brouillon ;
- `Bonnes pratiques` : services metier, separation draft/runtime, validations centralisees ;
- `RGPD` : ownership clair, donnees utilisateur identifiables, export/anonymisation extensibles ;
- `Resources & Request` : chaque endpoint du workflow a ses FormRequest/Resource ;
- `API securise` : on derive l'owner depuis le contexte auth, jamais depuis le payload ;
- `Documentation` : feature specifiable proprement en README/OpenAPI/Postman ;
- `Serialization` : reponses uniformes via `ApiResponse` ;
- `Gestion des relations` : liens draft coherents, publication transactionnelle, contraintes inter-entites.

# 2. Analyse de l'existant

## 2.1 Ce que l'API fait deja bien

Le projet a deja plusieurs bases solides :

- architecture Laravel 12 claire avec `Controllers`, `Models`, `Resources`, `FormRequests`, `Services` ;
- routes versionnees sous `/api/v1` ;
- enveloppe JSON standardisee via `ApiResponse` ;
- logique metier deja extraite pour les builds via `BuildRuleService` et `BuildSummaryService` ;
- schema plutot data-driven : `items.slot`, `item_slots.slot_key`, `item_slots.accepted_modifier_type`, `modifiers.modifier_type` ;
- relations builder deja generiques pour gerer plusieurs jeux sans hardcode par jeu.

Les fichiers les plus structurants pour la future feature sont :

- `routes/api.php`
- `app/Support/ApiResponse.php`
- `app/Http/Requests/ApiFormRequest.php`
- `app/Services/BuildRuleService.php`
- `doc/architecture_modifications.md`

## 2.2 Patterns actuels a respecter

La spec doit rester coherente avec ces conventions :

- endpoints REST simples, lisibles, sous `/api/v1` ;
- reponses enveloppees dans `{ success, message, data }` ;
- validation d'entree prioritairement via `ApiFormRequest` ;
- `Resources` pour la serialisation ;
- middleware custom deja presents (`check.api.key`, `check.role`) ;
- usage de services pour les regles metier non triviales.

## 2.3 Contraintes et limites reelles de l'existant

L'existant n'est pas mauvais, mais il a des limites importantes pour cette feature :

1. L'ownership est faible.

- Aujourd'hui, il n'y a pas de vrai systeme d'authentification utilisateur robuste pour une feature orientee brouillons/ownership.
- Le middleware de role lit `X-USER-ID` dans le header, ce qui est tres insuffisant pour une feature sensible.
- Des endpoints actuels acceptent encore `user_id` dans le payload, ce qui est dangereux pour une future creation de jeux par utilisateur.

2. `games` ne porte aujourd'hui aucune notion de draft, de publication, d'owner ou de visibilite.

- La table actuelle est faite pour des jeux deja exploitables.
- Si on y met des jeux incomplets, on casse les endpoints publics existants.

3. `categories` est actuellement global.

- C'est acceptable pour des datasets seedes maitrises.
- Ce n'est pas acceptable pour des jeux crees par les utilisateurs.
- Deux jeux utilisateurs differents auront fatalement besoin de categories locales sans collision globale de slug.

4. Le modele de stats est fixe (`score`, `attack`, `defense`, `utility`, `accuracy`, `mobility`).

- Pour un FPS, c'est encore acceptable.
- Pour un RPG, c'est deja limitant.
- On peut lancer une V1 avec ce modele si on assume une categorie RPG "canalisee", mais il faut dire clairement que l'expressivite RPG restera partielle tant qu'un systeme de stats configurables n'existe pas.

5. Le projet est en transition de vocabulaire.

- Le schema cible est bon (`archetypes`, `items`, `modifiers`, `categories`, `build_items`, `build_slots`).
- Mais plusieurs classes portent encore les anciens noms (`BuildProfile`, `EquipmentItem`, `EquipmentModifier`, `Tag`).
- La spec doit respecter cette realite et proposer des evolutions realistes, pas une refonte cosmetique gratuite.

## 2.4 Conclusion d'audit

La feature est faisable proprement sans casser l'existant a condition de :

- ne pas reutiliser `games` pour stocker les brouillons ;
- renforcer l'ownership des ecritures ;
- rendre `categories` scopees au jeu pour les jeux utilisateurs ;
- garder les templates de categorie au backend ;
- publier par conversion transactionnelle vers les tables runtime deja en place.

# 3. Besoin fonctionnel reformule

## 3.1 Besoin cible

Un utilisateur authentifie doit pouvoir creer un nouveau jeu dans une categorie predefinie (`fps`, `rpg`) via un parcours guide, persistant et valide par le backend.

Le systeme doit :

- guider la construction du dataset ;
- autoriser des brouillons incomplets ;
- empecher la publication tant que la structure n'est pas coherente ;
- permettre la reprise du travail plus tard ;
- garantir qu'un jeu publie est proprement exploitable par l'API et le builder.

## 3.2 Perimetre

Dans le perimetre de la feature :

- choix d'une categorie de jeu ;
- creation d'un brouillon ;
- sauvegarde progressive ;
- reprise du brouillon ;
- CRUD des entites de draft ;
- validation par etape ;
- publication d'un jeu valide ;
- ownership et autorisation ;
- visibilite initiale privee par defaut.

## 3.3 Hors perimetre

Hors perimetre pour la premiere implementation :

- moderation communautaire avancee ;
- marketplace de jeux publics communautaires ;
- edition "live" destructive d'un jeu publie ;
- systeme complet de versions runtime avec migration automatique des builds existants ;
- prise en charge d'un RPG totalement libre avec stats dynamiques illimitees.

## 3.4 Hypotheses explicites

- Un jeu cree par utilisateur ne doit pas apparaitre publiquement dans `GET /games` par defaut.
- La publication initiale rend le jeu utilisable par son proprietaire, pas automatiquement "global public".
- La V1 reste compatible avec le modele de stats actuel.
- Les templates `fps` et `rpg` sont definis dans le code backend et versionnes avec l'application.

# 4. Parcours utilisateur recommande

## 4.1 Vue d'ensemble

Le parcours recommande est un workflow guide par etapes avec validation intermediaire :

1. choisir une categorie ;
2. initialiser le draft ;
3. renseigner les fondations du jeu ;
4. confirmer ou ajuster le cadre structurel autorise par le template ;
5. definir les archetypes et classifications ;
6. creer les items ;
7. creer les modifiers et leurs compatibilites ;
8. verifier, corriger, publier.

## 4.2 Detail du workflow

### Etape 0 - Point d'entree

L'utilisateur ouvre la feature et recupere la liste des categories disponibles.

Le backend renvoie pour chaque categorie :

- label ;
- description ;
- version de template ;
- resume des contraintes ;
- minimum requis pour publication ;
- capacites supportees.

Pourquoi ce point d'entree :

- il cadre immediatement le jeu que l'utilisateur est en train de creer ;
- il evite l'illusion d'une liberte totale qui casserait le dataset ;
- il pose les attentes avant la premiere ecriture.

### Etape 1 - Initialisation du draft

L'utilisateur cree un draft avec :

- la categorie (`fps` ou `rpg`) ;
- un nom de travail ;
- eventuellement un slug propose.

Le backend :

- cree le draft ;
- fige `category_key` et `template_version` ;
- initialise les `step_states` ;
- applique les presets du template ;
- retourne `current_step`.

Pourquoi cette etape doit figer le template :

- changer de categorie en cours de route invaliderait toutes les relations dependantes ;
- il vaut mieux imposer "nouveau draft" que tenter des migrations fragiles.

### Etape 2 - Fondations

L'utilisateur remplit :

- nom final ;
- slug ;
- description courte ;
- eventuels metadonnees non critiques ;
- visibilite cible initiale (`private` par defaut).

Le backend valide :

- unicite du slug dans l'espace publie ;
- format du slug ;
- longueur et qualite minimale des champs requis ;
- interdiction de conflit avec un jeu systeme existant.

Le draft peut etre sauve meme si l'etape n'est pas complete, mais l'etape ne peut pas etre `complete` tant que les requis ne sont pas remplis.

### Etape 3 - Cadre structurel

Cette etape est critique. Elle ne doit pas etre libre.

L'utilisateur confirme les structures autorisees par le template :

- vocabulaire d'item global (`weapon`, `armor`, `skill`, etc. selon categorie) ;
- types de modifiers autorises ;
- capacites du jeu (`supports_modifiers`, `supports_archetypes`) ;
- minimum de classification locale.

Recommendation importante :

- les vocabulaires de base doivent venir du template ;
- l'utilisateur peut activer/desactiver certains presets selon ce que la categorie autorise ;
- l'utilisateur ne doit pas pouvoir inventer librement des types structurants a ce stade sans garde-fou.

Pourquoi :

- c'est le meilleur moyen d'empecher `weapon`, `main_weapon`, `primary`, `primary-weapon` d'exister en parallele ;
- c'est ici qu'on protege la normalisation du dataset.

### Etape 4 - Archetypes et categories locales

L'utilisateur cree ensuite :

- les archetypes du jeu ;
- les categories locales de classification des items du jeu.

L'ordre est volontaire :

- ces entites sont referencees ensuite partout ;
- elles doivent exister avant la creation massive d'items pour eviter les orphelins ou les reclassifications destructives.

### Etape 5 - Items

L'utilisateur cree les items du jeu.

Un item est saisi avec :

- un slot global autorise par le template ;
- ses stats ;
- ses categories locales ;
- ses `item_slots` si le template/capability autorise les modifiers.

Le backend bloque :

- un slot global inconnu ;
- des `item_slots` dupliques ;
- un `accepted_modifier_type` non autorise ;
- des stats manquantes selon la categorie ;
- des categories d'un autre draft ;
- un slug duplique.

### Etape 6 - Modifiers et compatibilites

L'utilisateur cree ensuite les modifiers.

Un modifier ne peut pas etre purement "decoratif" pour le dataset :

- il doit avoir un type autorise ;
- il doit modifier au moins un axe utile ou etre explicitement autorise par le template ;
- il doit etre rattache a au moins un item compatible avant publication.

Cette etape vient apres les items, car la compatibilite depend des items deja existants.

### Etape 7 - Revue et publication

Le backend calcule :

- les erreurs bloquantes ;
- les warnings ;
- la completion par etape ;
- la completion globale ;
- le premier blocage a corriger.

La publication :

- refuse si une etape bloquante n'est pas complete ;
- refuse si le minimum requis de la categorie n'est pas atteint ;
- convertit le draft en runtime dataset dans une transaction ;
- marque le draft comme `published`.

## 4.3 Sauvegarde, reprise, verrouillage

Chaque ecriture sauvegarde immediatement en base.

Pour la reprise :

- `current_step` est derive du premier step bloquant ;
- `step_states` conservent `status`, `last_saved_at`, `last_validated_at`, `blocking_errors_count` ;
- l'utilisateur peut revenir plus tard sans perdre le contexte.

Verrouillages recommandes :

- `category_key` et `template_version` immuables apres creation du draft ;
- changement du cadre structurel interdit si des items ou modifiers existent deja, sauf reset explicite ;
- suppression d'une entite referencee refusee tant qu'il existe des dependances.

## 4.4 Pourquoi ce workflow plutot qu'un CRUD libre

Je recommande ce parcours parce qu'il :

- suit l'ordre logique des dependances metier ;
- permet des brouillons sans casser l'API live ;
- empeche les incoherences structurelles trop tot ;
- reste comprehensible cote front ;
- est extensible a d'autres categories sans changer le principe.

Un CRUD libre sur `games`, `items`, `modifiers` serait plus simple a coder mais beaucoup plus dangereux pour la qualite des donnees.

# 5. Regles metier et garde-fous

## 5.1 Regles transverses

- un draft appartient a un seul utilisateur ;
- aucun endpoint n'accepte `owner_user_id` ou `user_id` pour fixer le proprietaire ;
- toutes les entites d'un draft doivent appartenir au meme draft ;
- la categorie du draft est figee ;
- le template backend est la source de verite des structures autorisees ;
- un jeu publie n'est jamais alimente par des lignes partielles.

## 5.2 Regles de coherence de nommage

- slug unique par draft pour chaque type d'entite ;
- slug unique par jeu publie pour chaque type d'entite ;
- vocabulaire structurel restreint a des valeurs autorisees par template ;
- interdiction des doublons insensibles a la casse sur les noms structurants ;
- normalisation auto du slug a la sauvegarde.

## 5.3 Regles sur les relations

- un item ne reference que des categories du meme draft ;
- un item_slot appartient a un item du meme draft ;
- un modifier ne peut cibler que des items du meme draft ;
- un `accepted_modifier_type` doit etre un type autorise par le template ;
- une compatibilite item/modifier ne peut pas exister si les types ne matchent pas ;
- suppression d'un item impossible s'il est encore reference par une compatibilite active, sauf suppression en cascade explicite dans le draft ;
- suppression d'une categorie impossible si elle laisse un item sans classification minimale requise.

## 5.4 Regles de progression

- un step peut etre `not_started`, `in_progress`, `complete`, `blocked` ;
- un step est `complete` uniquement apres validation backend ;
- un step en amont modifie peut invalider les steps aval ;
- `publish` exige que tous les steps bloquants soient `complete`.

## 5.5 Minima de publication recommandes

### Template FPS

- 1 fondation complete ;
- 1 cadre structurel valide ;
- au moins 1 archetype ;
- au moins 2 categories locales ;
- au moins 4 items ;
- parmi ces items, au moins 1 item de slot `weapon` ;
- si `supports_modifiers = true`, au moins 3 modifiers ;
- tout modifier publie doit etre compatible avec au moins 1 item ;
- aucun item de type `weapon` ne peut avoir un `item_slot` dont le type accepte un modifier inexistant.

### Template RPG

- 1 fondation complete ;
- 1 cadre structurel valide ;
- au moins 1 archetype ;
- au moins 2 categories locales ;
- au moins 6 items ;
- au moins 2 slots globaux actifs parmi ceux permis par le template ;
- si `supports_modifiers = true`, au moins 3 modifiers compatibles ;
- tous les items publies doivent etre classifies.

## 5.6 Refus de publication

La publication doit echouer si :

- des erreurs bloquantes existent ;
- le slug entre en collision avec un jeu publie ;
- des dependances sont orphelines ;
- le minimum requis du template n'est pas atteint ;
- un step aval depend d'un step amont invalide ;
- la transaction de promotion ne peut pas ecrire un dataset complet.

# 6. Modele de donnees recommande

## 6.1 Principe general

Je recommande de ne pas melanger brouillon et runtime.

Le modele doit donc avoir :

- des tables draft dediees ;
- les tables runtime existantes conservees pour les jeux utilisables ;
- un petit nombre d'evolutions sur les tables runtime pour ownership/visibilite.

## 6.2 Nouvelles tables de draft

### `game_drafts`

Colonnes recommandees :

- `id`
- `owner_user_id`
- `published_game_id` nullable
- `category_key` (`fps`, `rpg`)
- `template_version`
- `status` (`draft`, `in_progress`, `ready`, `published`, `archived`)
- `current_step`
- `name`
- `slug`
- `description`
- `visibility` (`private`, `public_pending`)
- `completion_percent` cache nullable
- `last_validated_at` nullable
- `published_at` nullable
- `settings` json nullable
- `meta` json nullable
- timestamps

### `game_draft_steps`

- `id`
- `game_draft_id`
- `step_key`
- `status`
- `blocking_errors_count`
- `warnings_count`
- `last_saved_at`
- `last_validated_at`
- `payload_snapshot` json nullable
- unique `(game_draft_id, step_key)`

### `game_draft_archetypes`

- `id`
- `game_draft_id`
- `name`
- `slug`
- `description`

### `game_draft_categories`

- `id`
- `game_draft_id`
- `label`
- `slug`
- `description` nullable

### `game_draft_items`

- `id`
- `game_draft_id`
- `name`
- `slug`
- `slot`
- `rarity`
- `tier_level`
- `score`
- `attack`
- `defense`
- `utility`
- `accuracy`
- `mobility`
- `metadata` json nullable

### `game_draft_item_slots`

- `id`
- `game_draft_item_id`
- `slot_key`
- `slot_label`
- `accepted_modifier_type` nullable
- `sort_order`
- `is_required`
- `metadata` json nullable

### `game_draft_modifiers`

- `id`
- `game_draft_id`
- `name`
- `slug`
- `modifier_type`
- `description`
- `delta_score`
- `delta_attack`
- `delta_defense`
- `delta_utility`
- `delta_accuracy`
- `delta_mobility`
- `metadata` json nullable

### Tables pivot de draft

- `game_draft_item_categories`
- `game_draft_item_modifiers`

## 6.3 Evolutions recommandees sur le runtime

### `games`

Ajouter :

- `owner_user_id` nullable
- `creation_category` string(40)
- `source_type` string(20) (`system`, `user`)
- `visibility` string(20) (`public`, `private`)
- `published_at` nullable

But :

- conserver le runtime existant ;
- distinguer les jeux seeds et les jeux utilisateurs ;
- eviter qu'un jeu utilisateur prive sorte dans les endpoints publics standards.

### `categories`

Evolution recommandee :

- ajouter `game_id` ;
- sortir d'une categorie globale partagee ;
- poser une unicite scopee `(game_id, slug)` ;
- poser idealement une unicite `(game_id, label)`.

Pourquoi cette evolution est necessaire :

- des categories globales uniques ne tiennent pas quand des utilisateurs creent leurs propres jeux ;
- la classification doit etre locale au jeu si on veut eviter les collisions semantiques.

## 6.4 Ce qui doit etre stocke

Doit etre stocke :

- le draft lui-meme ;
- les entites de draft ;
- l'etat des steps ;
- l'owner ;
- le template choisi ;
- les erreurs/warnings de validation les plus recentes ;
- le lien eventuel vers le jeu publie.

## 6.5 Ce qui ne doit pas etre stocke

Ne doit pas etre stocke en base a ce stade :

- la definition complete des templates `fps` / `rpg` ;
- les regles metier executees ;
- la logique de progression.

Ces elements doivent rester dans le code backend versionne.

## 6.6 Ce qui doit etre derive ou calcule

Doit etre calcule :

- `current_step`
- `completion_percent`
- `publish_ready`
- les erreurs bloquantes par step
- le resume de completude

On peut les mettre en cache dans le draft pour lecture rapide, mais la source de verite reste le recalcul backend.

## 6.7 Versioning

Recommendation pragmatique :

- V1 : un draft initial publie une premiere version du jeu ;
- l'edition structurelle d'un jeu publie ne modifie pas directement le runtime ;
- on prepare le terrain avec `published_game_id` pour une future notion de revision.

Je deconseille fortement de modifier destructivement un jeu publie en place tant que les builds existants n'ont pas de strategie de compatibilite/version.

# 7. Strategie de persistance

## 7.1 Brouillon

Je recommande un systeme de brouillon persistant en base, pas un blob JSON unique, pas un stockage frontend.

Pourquoi :

- il faut valider les relations ;
- il faut reprendre le travail ;
- il faut auditer ;
- il faut publier transactionnellement ;
- il faut rester industrialisable.

## 7.2 Brouillon unique ou multiples brouillons

Recommendation :

- plusieurs brouillons possibles par utilisateur ;
- un seul brouillon actif par "ligne de jeu" si plus tard on introduit les revisions ;
- pas de brouillons anonymes.

## 7.3 Sauvegarde partielle

Chaque endpoint de mutation doit sauvegarder partiellement sans exiger la completion totale du draft.

En revanche :

- les references doivent toujours etre coherentes ;
- les slugs doivent deja respecter les contraintes ;
- les donnees cassantes ne doivent pas etre stockees "n'importe comment".

## 7.4 Reprise

La reprise doit s'appuyer sur :

- `GET /game-drafts/{id}` pour la vue globale ;
- `step_states` pour afficher l'etape en cours ;
- `current_step` calcule ;
- les erreurs bloquantes persistables ou recalculables.

## 7.5 Publication

La publication doit faire :

1. validation globale finale ;
2. ouverture d'une transaction ;
3. creation du `game` runtime ;
4. insertion des `archetypes`, `categories`, `items`, `item_slots`, `modifiers` ;
5. insertion des pivots `item_categories`, `item_modifiers` ;
6. mise a jour du draft (`status = published`, `published_game_id`, `published_at`) ;
7. commit.

Si une etape echoue :

- rollback complet ;
- aucun runtime partiel ne doit subsister.

## 7.6 Snapshots

Recommendation utile :

- stocker un `publication_snapshot` JSON sur le draft au moment de la publication ;
- cela facilite debug, audit, documentation et export.

# 8. Design API backend

## 8.1 Vocabulaire API recommande

Pour rester coherent avec l'existant :

- `game-categories` pour les templates de creation ;
- `game-drafts` pour les brouillons ;
- `draft categories/items/modifiers/archetypes` comme sous-ressources du draft ;
- `publish` comme action metier explicite.

## 8.2 Endpoints a creer

### Lecture des templates

- `GET /api/v1/game-categories`
- `GET /api/v1/game-categories/{key}`

### Gestion des drafts

- `GET /api/v1/game-drafts`
- `POST /api/v1/game-drafts`
- `GET /api/v1/game-drafts/{id}`
- `DELETE /api/v1/game-drafts/{id}`

### Etapes guidees

- `PATCH /api/v1/game-drafts/{id}/foundation`
- `PATCH /api/v1/game-drafts/{id}/structure`
- `POST /api/v1/game-drafts/{id}/steps/{stepKey}/validate`
- `POST /api/v1/game-drafts/{id}/publish`

### Sous-ressources de draft

- `GET|POST|PATCH|DELETE /api/v1/game-drafts/{id}/archetypes`
- `GET|POST|PATCH|DELETE /api/v1/game-drafts/{id}/categories`
- `GET|POST|PATCH|DELETE /api/v1/game-drafts/{id}/items`
- `GET|POST|PATCH|DELETE /api/v1/game-drafts/{id}/modifiers`

### Lecture owner des jeux publies

- `GET /api/v1/my/games`
- `GET /api/v1/my/games/{id}`

Recommendation :

- ne pas exposer les jeux prives utilisateurs dans `GET /api/v1/games` par defaut.

## 8.3 Exemples de payloads

### Creation d'un draft

```json
{
  "category_key": "fps",
  "name": "My Arena Shooter",
  "slug": "my-arena-shooter"
}
```

### Mise a jour fondation

```json
{
  "name": "My Arena Shooter",
  "slug": "my-arena-shooter",
  "description": "Fast competitive FPS with modular weapons.",
  "visibility": "private"
}
```

### Mise a jour structure

```json
{
  "supports_modifiers": true,
  "supports_archetypes": true,
  "active_item_slots": ["weapon", "secondary", "equipment"],
  "allowed_modifier_types": ["optic", "muzzle", "magazine", "stock"]
}
```

### Creation d'un item de draft

```json
{
  "name": "VX-7",
  "slug": "vx-7",
  "slot": "weapon",
  "rarity": "standard",
  "tier_level": "T1",
  "score": 78,
  "attack": 72,
  "defense": 10,
  "utility": 18,
  "accuracy": 65,
  "mobility": 58,
  "category_ids": [12, 13],
  "item_slots": [
    {
      "slot_key": "optic",
      "slot_label": "Optic",
      "accepted_modifier_type": "optic",
      "sort_order": 1,
      "is_required": false
    }
  ]
}
```

### Creation d'un modifier de draft

```json
{
  "name": "Red Dot X",
  "slug": "red-dot-x",
  "modifier_type": "optic",
  "description": "Clean short range optic",
  "delta_score": 2,
  "delta_attack": 0,
  "delta_defense": 0,
  "delta_utility": 0,
  "delta_accuracy": 6,
  "delta_mobility": -1,
  "compatible_item_ids": [44, 45]
}
```

### Validation d'etape

`POST /api/v1/game-drafts/{id}/steps/items/validate`

Reponse :

```json
{
  "success": true,
  "message": "Step validated.",
  "data": {
    "step_key": "items",
    "status": "complete",
    "blocking_errors": [],
    "warnings": [
      "Only one weapon currently has attachment slots."
    ]
  }
}
```

## 8.4 Validation backend

Chaque endpoint d'ecriture doit avoir :

- un `FormRequest` dedie ;
- une validation de forme ;
- une validation metier via services ;
- une verification d'ownership.

Exemples :

- `StoreGameDraftRequest`
- `UpdateGameDraftFoundationRequest`
- `UpdateGameDraftStructureRequest`
- `StoreGameDraftItemRequest`
- `StoreGameDraftModifierRequest`
- `ValidateGameDraftStepRequest`
- `PublishGameDraftRequest`

## 8.5 Regles d'autorisation

Recommendation minimale compatible avec l'existant :

- toutes les routes draft sous `check.api.key` ;
- resolution du user appelant via middleware dedie ;
- owner ou admin uniquement pour lecture/ecriture de son draft ;
- publication reservee a l'owner ;
- publication publique globale reservee a un workflow futur, ou a un admin.

Important :

- ne jamais lire l'owner depuis le body ;
- ne jamais permettre `owner_user_id` en payload ;
- pour cette feature, il faut introduire soit un vrai auth token, soit au minimum un middleware `ResolveRequestUser` centralisant la lecture du user et l'injection dans la requete.

## 8.6 Codes de statut

- `200` lecture / mise a jour / validation
- `201` creation de draft ou publication reussie
- `204` suppression
- `401` credentials/API key manquants
- `403` non owner ou role insuffisant
- `404` draft ou ressource introuvable
- `409` etat incompatible du workflow (step verrouille, draft deja publie)
- `422` validation ou regle metier violee

## 8.7 Erreurs metier recommandees

Exemples de codes d'erreur metier stables :

- `draft_category_locked`
- `draft_structure_locked_by_existing_items`
- `draft_publish_blocked`
- `draft_missing_required_step`
- `draft_invalid_item_slot`
- `draft_invalid_modifier_type`
- `draft_cross_scope_relation`
- `draft_slug_conflict`
- `draft_minimum_dataset_not_reached`

Recommendation :

- conserver l'enveloppe `ApiResponse` ;
- ajouter des `error_codes` machine-friendly dans `errors`.

# 9. Risques et arbitrages

## 9.1 Risque majeur : reutiliser les tables live pour les brouillons

C'est une mauvaise idee.

Cela casserait :

- les endpoints publics existants ;
- la qualite du dataset ;
- la lisibilite du modele ;
- la publication atomique.

## 9.2 Risque majeur : ownership trop faible

Avec l'etat actuel de l'auth, cette feature est sensible.

Si tu la sors sans resoudre ce point, tu prends un risque direct sur :

- securite ;
- ownership ;
- note du prof sur la partie "API securise".

La recommendation est claire :

- au minimum, owner derive du contexte d'auth ;
- idealement, vraie auth (Sanctum/JWT).

## 9.3 Risque majeur : categories globales

Si tu gardes `categories` globales et uniques pour des jeux utilisateurs, tu vas melanger des classifications sans controle.

Je recommande explicitement une scoping par jeu.

## 9.4 Risque majeur : RPG sur modele de stats fixe

Le projet sait gerer des stats fixes. Il ne sait pas encore exprimer un RPG riche de maniere native.

Arbitrage recommande :

- V1 possible avec RPG cadre et assume ;
- evolution future vers des stats configurables si tu veux un vrai support RPG industrialisable.

## 9.5 Arbitrage sur l'edition des jeux publies

Modifier un jeu publie en place est dangereux pour les builds existants.

Recommendation :

- V1 : publication initiale propre, pas d'edition structurelle destructive ;
- V2 : revision draft si besoin.

# 10. Recommandation d'implementation

Ordre technique conseille :

1. renforcer l'identification utilisateur pour les ecritures ;
2. introduire le modele draft ;
3. introduire le registre de templates backend ;
4. implementer les validations de progression ;
5. implementer les CRUD draft sur les entites ;
6. implementer la publication transactionnelle ;
7. filtrer les jeux publies prives/publics dans les endpoints de lecture ;
8. couvrir par tests et documenter.

Pourquoi cet ordre :

- il de-risque d'abord l'ownership et la structure ;
- il pose d'abord les fondations de donnees ;
- il retarde la publication au moment ou les garde-fous existent deja.

# 11. Decoupage en petites taches

## B-001 - Audit technique cible

- Objectif : figer la cible de schema et les ecarts avec l'existant.
- Description : documenter les tables/runtime impactees (`games`, `categories`) et le besoin de tables draft.
- Dependances : aucune.
- Livrable attendu : note d'architecture ou ADR courte.
- Criteres d'acceptation : ecarts schema/auth identifies et valides.
- Complexite estimee : S.
- Priorite : haute.

## B-002 - Renforcer l'identification utilisateur

- Objectif : ne plus accepter d'ownership depuis le payload.
- Description : ajouter un middleware de resolution utilisateur et une convention unique pour recuperer le user appelant.
- Dependances : B-001.
- Livrable attendu : middleware + tests.
- Criteres d'acceptation : les endpoints de draft connaissent `request_user`; aucun `owner_user_id` n'est pris depuis le body.
- Complexite estimee : M.
- Priorite : haute.

## B-003 - Faire evoluer `games` pour ownership/visibilite

- Objectif : distinguer jeux systeme, jeux utilisateur et visibilite.
- Description : migration ajoutant `owner_user_id`, `creation_category`, `source_type`, `visibility`, `published_at`.
- Dependances : B-001.
- Livrable attendu : migration + modele `Game` ajuste.
- Criteres d'acceptation : un jeu utilisateur publie peut etre scope a son owner sans sortir dans le listing public par defaut.
- Complexite estimee : M.
- Priorite : haute.

## B-004 - Rendre `categories` scopees au jeu

- Objectif : eviter les collisions de classification entre jeux.
- Description : migration et adaptation model/controller/resource pour passer d'une unicite globale a une unicite par jeu.
- Dependances : B-001.
- Livrable attendu : migration + adaptations de lecture/ecriture.
- Criteres d'acceptation : deux jeux differents peuvent avoir une categorie `assault-rifle` sans collision globale.
- Complexite estimee : M.
- Priorite : haute.

## B-005 - Creer le schema draft

- Objectif : isoler les brouillons du runtime.
- Description : migrations pour `game_drafts`, `game_draft_steps`, `game_draft_archetypes`, `game_draft_categories`, `game_draft_items`, `game_draft_item_slots`, `game_draft_modifiers`, pivots.
- Dependances : B-001.
- Livrable attendu : migrations et contraintes DB.
- Criteres d'acceptation : un draft complet peut etre persiste sans toucher les tables runtime.
- Complexite estimee : L.
- Priorite : haute.

## B-006 - Implementer les models draft

- Objectif : disposer d'un aggregate Eloquent propre.
- Description : models, relations, casts, scopes de base.
- Dependances : B-005.
- Livrable attendu : models draft + tests unitaires de relations.
- Criteres d'acceptation : les relations inter-entites draft fonctionnent proprement.
- Complexite estimee : M.
- Priorite : haute.

## B-007 - Implementer le registre de templates categorie

- Objectif : centraliser les regles `fps` / `rpg`.
- Description : config + services `GameCategoryTemplateRegistry`.
- Dependances : B-001.
- Livrable attendu : definitions versionnees des templates.
- Criteres d'acceptation : l'API peut lister les categories et leurs contraintes sans lire la DB.
- Complexite estimee : M.
- Priorite : haute.

## B-008 - Implementer les services de validation de progression

- Objectif : calculer `current_step`, completion, erreurs, readiness.
- Description : services `GameDraftValidationService`, `GameDraftProgressService`.
- Dependances : B-005, B-006, B-007.
- Livrable attendu : services + tests metier.
- Criteres d'acceptation : un draft incomplet produit un diagnostic deterministe et reproductible.
- Complexite estimee : L.
- Priorite : haute.

## B-009 - Exposer l'API des drafts

- Objectif : CRUD du brouillon et lecture de progression.
- Description : controllers, requests, resources pour `game-drafts`.
- Dependances : B-002, B-006, B-008.
- Livrable attendu : endpoints draft documentes.
- Criteres d'acceptation : creation, lecture, suppression, reprise d'un draft fonctionnent.
- Complexite estimee : M.
- Priorite : haute.

## B-010 - Exposer les endpoints etape par etape

- Objectif : guider la saisie sans front libre.
- Description : endpoints `foundation`, `structure`, `validate-step`.
- Dependances : B-009.
- Livrable attendu : controllers/requests/resources dedies.
- Criteres d'acceptation : les steps sont sauvegardables et validables independamment.
- Complexite estimee : M.
- Priorite : haute.

## B-011 - Exposer les sous-ressources draft

- Objectif : gerer archetypes, categories, items, modifiers du draft.
- Description : CRUD dedie par sous-ressource avec ownership et regles metier.
- Dependances : B-009, B-010.
- Livrable attendu : endpoints sous-ressources + validation backend.
- Criteres d'acceptation : impossible de creer une relation cross-draft ou hors template.
- Complexite estimee : L.
- Priorite : haute.

## B-012 - Implementer la publication transactionnelle

- Objectif : convertir un draft valide en jeu runtime.
- Description : service `GameDraftPublicationService` responsable du mapping draft -> runtime.
- Dependances : B-008, B-011, B-003, B-004.
- Livrable attendu : endpoint `publish` + service transactionnel.
- Criteres d'acceptation : en succes, le jeu runtime est complet ; en echec, aucun residu runtime.
- Complexite estimee : L.
- Priorite : haute.

## B-013 - Filtrer correctement les jeux publics et prives

- Objectif : ne pas exposer les jeux utilisateurs prives dans les endpoints publics.
- Description : adaptation des queries `GameController` et endpoints owner.
- Dependances : B-003, B-012.
- Livrable attendu : lecture publique et lecture owner separees.
- Criteres d'acceptation : `GET /games` n'expose pas les jeux prives utilisateur.
- Complexite estimee : M.
- Priorite : haute.

## B-014 - Aligner la feature sur les bonnes pratiques Laravel

- Objectif : maximiser la note du prof.
- Description : requests partout, resources partout, serialisation uniforme, services metier, tests.
- Dependances : B-009 a B-013.
- Livrable attendu : code homogene avec l'existant cible.
- Criteres d'acceptation : aucun endpoint draft n'utilise de validation inline improvisee.
- Complexite estimee : M.
- Priorite : haute.

## B-015 - Couvrir par tests feature

- Objectif : securiser la feature et prouver la qualite.
- Description : tests d'ownership, de progression, de publication, de refus de publication, de visibilite.
- Dependances : B-009 a B-013.
- Livrable attendu : suite de tests Pest/Feature.
- Criteres d'acceptation : cas nominaux et cas bloquants couverts.
- Complexite estimee : L.
- Priorite : haute.

## B-016 - Etendre la doc projet

- Objectif : rendre la feature evaluable facilement.
- Description : README + doc API + exemples Postman/OpenAPI.
- Dependances : B-009 a B-015.
- Livrable attendu : documentation d'utilisation/deploiement et parcours draft/publish.
- Criteres d'acceptation : un correcteur peut lancer, tester et comprendre la feature sans fouiller le code.
- Complexite estimee : M.
- Priorite : haute.

## B-017 - Preparer le futur front

- Objectif : simplifier l'implementation UI guidee plus tard.
- Description : fournir des payloads de progression, erreurs bloquees, warnings, next step.
- Dependances : B-008, B-010.
- Livrable attendu : responses API pensees pour wizard front.
- Criteres d'acceptation : le front n'a pas besoin de rederiver la logique metier.
- Complexite estimee : M.
- Priorite : moyenne.

## B-018 - Etudier l'evolution future des stats RPG

- Objectif : preparer la vraie extensibilite categorie.
- Description : ADR sur une future normalisation de stats configurables.
- Dependances : aucune pour la V1.
- Livrable attendu : note technique.
- Criteres d'acceptation : decision claire sur maintien du modele fixe ou evolution.
- Complexite estimee : M.
- Priorite : moyenne.

# 12. Prochaine etape

La prochaine etape utile apres validation de cette spec est de figer trois decisions produit/architecture avant de coder :

1. un jeu utilisateur publie est-il `private` par defaut ou visible publiquement ;
2. la V1 supporte-t-elle un `RPG cadre` sur les 6 stats actuelles, ou `FPS publishable / RPG draft only` ;
3. veut-on une vraie auth utilisateur maintenant, ou un renforcement temporaire du systeme actuel pour passer proprement le rendu.

Une fois ces trois arbitrages valides, la bonne suite est :

- ecrire une courte ADR ;
- implementer B-002 a B-005 ;
- puis seulement ouvrir les endpoints draft.
