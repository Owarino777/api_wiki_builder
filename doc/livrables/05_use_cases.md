# Use cases (version rendue)

## Systeme cible

Wiki Builder API (Laravel, front Blade, API REST).

## Acteurs

- Visiteur
- Utilisateur authentifie
- Admin
- Systeme externe (client API)

## UC-01 - Consulter le catalogue public

- Acteur principal : Visiteur / Systeme externe
- Precondition : API disponible
- Scenario nominal :
  1. L'acteur appelle `/api/v1/games`, `/api/v1/items`, `/api/v1/modifiers`.
  2. Le systeme renvoie les ressources publiques.
- Postcondition : catalogue consultable sans authentification.

## UC-02 - Composer un build

- Acteur principal : Visiteur / Utilisateur
- Precondition : au moins un jeu charge
- Scenario nominal :
  1. Selection d'un jeu.
  2. Chargement optionnel d'un build public.
  3. Ajout d'items dans 1 a 3 slots.
  4. Configuration des modifiers compatibles.
  5. Affichage des stats cumulees.
- Postcondition : build compose localement dans le builder.

## UC-03 - Comparer des slots

- Acteur principal : Visiteur / Utilisateur
- Precondition : au moins 2 slots charges
- Scenario nominal :
  1. L'acteur ouvre la matrice.
  2. Le systeme calcule les leaders par stat.
  3. Le systeme affiche le tableau compare.
- Postcondition : decision de build facilitee.

## UC-04 - Creer un draft de jeu

- Acteur principal : Utilisateur authentifie
- Precondition : utilisateur connecte + contexte user resolu
- Scenario nominal :
  1. L'utilisateur ouvre le studio.
  2. Selection d'une categorie/template.
  3. Soumission du formulaire draft.
  4. Le systeme cree `game_draft` avec ownership.
- Postcondition : draft persiste et editable.

## UC-05 - Editer un draft

- Acteur principal : Utilisateur authentifie
- Precondition : draft appartenant a l'utilisateur
- Scenario nominal :
  1. Mise a jour foundation.
  2. Mise a jour structure.
  3. CRUD archetypes/categories/items/modifiers.
  4. Verification progression.
- Postcondition : draft completable et reprenable.

## UC-06 - Publier un draft

- Acteur principal : Utilisateur authentifie
- Precondition : draft valide et publish-ready
- Scenario nominal :
  1. L'utilisateur declenche `POST /game-drafts/{id}/publish`.
  2. Le systeme verifie les erreurs bloquantes.
  3. Le systeme execute la publication en transaction.
  4. Le draft passe en `published`.
- Scenario d'exception :
  - si regles non satisfaites, reponse 422 "Draft publication blocked".
- Postcondition : jeu runtime cree ou rollback total.

## UC-07 - Gerer son compte RGPD

- Acteur principal : Utilisateur authentifie
- Precondition : session ouverte
- Scenario nominal :
  1. Export des donnees personnelles.
  2. Option anonymisation (mot de passe requis).
  3. Option suppression definitive (mot de passe + confirmation).
- Postcondition : conformite RGPD operationnelle cote compte.

## UC-08 - Administrer les ressources globales

- Acteur principal : Admin
- Precondition : API key valide + role admin
- Scenario nominal :
  1. CRUD categories/items/modifiers/archetypes.
  2. Gestion des compatibilites item-modifier.
  3. Actions RGPD admin ciblees.
- Postcondition : gouvernance dataset systeme maintenue.

## Traçabilite API rapide

- UC-01 : `GET /api/v1/games`, `GET /api/v1/items`, `GET /api/v1/modifiers`
- UC-02/UC-03 : home builder + `GET /api/v1/builds/*`, `POST /api/v1/builds/compare`
- UC-04/UC-05/UC-06 : `POST/PATCH/GET /api/v1/game-drafts/*`
- UC-07 : routes web `/account/*`
- UC-08 : routes API admin sous `check.role:admin`

