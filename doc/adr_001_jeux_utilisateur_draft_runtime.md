# ADR-001 - Jeux utilisateur via draft guide et publication runtime

## Statut

Retenu pour implementation preparatoire.

## Date

2026-03-16

## Contexte

L'API actuelle expose directement des jeux runtime via :

- `games`
- `archetypes`
- `items`
- `item_slots`
- `modifiers`
- `categories`
- pivots de compatibilite et de build

Ce modele fonctionne pour des jeux verifies, seeds ou importes, mais il n'est pas adapte a une creation libre par l'utilisateur.

Le besoin produit impose :

- un parcours guide ;
- de la sauvegarde partielle ;
- de la reprise ;
- de la validation metier forte ;
- un refus de publication en cas de dataset incoherent.

Si on ecrit directement dans les tables runtime, on prend un risque fort :

- pollution de l'API publique par des jeux incomplets ;
- relations orphelines ;
- categories incoherentes ;
- ownership fragile ;
- regressions sur le builder.

## Decision

### 1. Separation stricte entre draft et runtime

La creation utilisateur passe par un aggregate de brouillon dedie :

- `game_drafts`
- sous-entites de draft

Les tables runtime existantes restent reservees aux jeux exploitables par l'application.

### 2. Publication transactionnelle

Un draft valide est promu vers le runtime via une transaction unique.

Soit :

- tout le jeu runtime est cree correctement ;
- soit rien n'est publie.

### 3. Templates de categorie portes par le backend

Les categories de creation (`fps`, `rpg`) ne sont pas des donnees libres en base.

Leurs contraintes vivent cote backend dans un registre de templates versionne.

### 4. Ownership derive du contexte d'auth

L'owner d'un draft ou d'un jeu utilisateur ne vient jamais du payload.

Il est resolu par le backend via le contexte de requete.

### 5. Jeux utilisateur prives par defaut

Un jeu publie par un utilisateur est considere `private` par defaut en V1.

Il n'apparait pas automatiquement dans `GET /api/v1/games` public.

### 6. Categories runtime scopees au jeu

Pour eviter les collisions semantiques entre jeux utilisateurs, les categories runtime deviennent scopees a un jeu.

La cible retenue est :

- une categorie appartient a un seul `game_id`
- unicite par `(game_id, slug)`

### 7. Pas d'edition structurelle destructive en place sur un jeu publie

La V1 ne modifie pas destructivement un jeu publie en place.

Toute future evolution structurelle importante devra passer par une logique de revision de draft.

## Consequences positives

- meilleure qualite de donnees ;
- parcours utilisateur propre et reprenable ;
- publication sure ;
- compatibilite forte avec les bonnes pratiques Laravel deja presentes ;
- meilleure defense face au bareme du prof sur les relations, la securite, les requests/resources et la documentation.

## Consequences negatives

- plus de tables ;
- plus de services metier ;
- migration de `categories` plus delicate que le reste ;
- besoin de renforcer le mecanisme d'identification utilisateur avant ouverture large de la feature.

## Alternatives ecartees

### Alternative A - Ecriture directe dans `games/items/modifiers`

Refusee car trop dangereuse pour la coherence du runtime.

### Alternative B - Brouillon JSON unique dans une seule table

Refusee car pauvre en relations, mauvaise pour la validation inter-entites et la publication propre.

### Alternative C - Categories globales communes a tous les jeux

Refusee car incompatible avec des jeux utilisateurs heterogenes.

### Alternative D - Publication publique immediate

Refusee en V1 car trop risquee sans moderation ni ownership robuste.

## Consequences d'implementation

Les prochains chantiers doivent suivre cet ordre :

1. renforcer l'identification utilisateur ;
2. etendre `games` pour ownership/visibilite ;
3. migrer `categories` vers un scope par jeu ;
4. creer le schema draft ;
5. exposer le workflow draft ;
6. implementer la publication transactionnelle.

## References

- `doc/spec_ajout_jeux_utilisateur.md`
- `doc/architecture.md`
- `doc/architecture_modifications.md`
