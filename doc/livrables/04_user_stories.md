# User stories et backlog fonctionnel

## Format

- ID
- Story
- Priorite
- Critere d'acceptation principal

## Epic A - Acces et securite

### US-A1

En tant que visiteur, je veux creer un compte pour acceder au studio.

- Priorite : Haute
- AC : compte cree, redirection dashboard, session ouverte.

### US-A2

En tant qu'utilisateur, je veux me connecter pour retrouver mes drafts.

- Priorite : Haute
- AC : login valide, acces `/dashboard` et `/studio/games/create`.

### US-A3

En tant qu'utilisateur, je veux me deconnecter pour fermer ma session.

- Priorite : Haute
- AC : session invalidee, retour page publique.

## Epic B - Builder public

### US-B1

En tant qu'utilisateur, je veux choisir un jeu pour filtrer le catalogue.

- Priorite : Haute
- AC : items, builds, modifiers mis a jour selon le jeu selectionne.

### US-B2

En tant qu'utilisateur, je veux charger un build public pour partir d'une base.

- Priorite : Haute
- AC : slots et modifiers du build charges sans erreur.

### US-B3

En tant qu'utilisateur, je veux ajouter/remplacer des items dans le builder.

- Priorite : Haute
- AC : ajout sur slot libre ou remplacement guide si plein.

### US-B4

En tant qu'utilisateur, je veux comparer mes slots pour choisir le meilleur build.

- Priorite : Haute
- AC : matrice visible seulement a partir de 2 slots charges.

### US-B5

En tant qu'utilisateur, je veux filtrer rapidement le catalogue.

- Priorite : Haute
- AC : recherche + filtres principaux + filtres avances fonctionnels.

## Epic C - Studio drafts

### US-C1

En tant qu'utilisateur, je veux creer un draft base sur une categorie.

- Priorite : Haute
- AC : draft cree avec owner, template, etape initiale.

### US-C2

En tant qu'utilisateur, je veux sauvegarder la fondation de mon draft.

- Priorite : Haute
- AC : name/slug/description/visibility valides et persistes.

### US-C3

En tant qu'utilisateur, je veux definir la structure du draft.

- Priorite : Haute
- AC : settings valides, verrouillage si items/modifiers existants.

### US-C4

En tant qu'utilisateur, je veux gerer archetypes/categories/items/modifiers.

- Priorite : Haute
- AC : CRUD complet avec validation et ownership.

### US-C5

En tant qu'utilisateur, je veux publier un draft complet.

- Priorite : Haute
- AC : publication refusee si erreurs, sinon creation runtime transactionnelle.

## Epic D - Compte et RGPD

### US-D1

En tant qu'utilisateur, je veux exporter mes donnees.

- Priorite : Haute
- AC : export JSON telechargeable via route dediee.

### US-D2

En tant qu'utilisateur, je veux anonymiser mon compte.

- Priorite : Haute
- AC : mot de passe requis, donnees nominatives neutralisees.

### US-D3

En tant qu'utilisateur, je veux supprimer mon compte.

- Priorite : Haute
- AC : confirmation explicite + mot de passe obligatoire.

## Epic E - Gouvernance admin API

### US-E1

En tant qu'admin, je veux creer/editer/supprimer les ressources globales.

- Priorite : Moyenne
- AC : routes write admin operationnelles sous `check.role:admin`.

### US-E2

En tant qu'admin, je veux executer les actions RGPD cibles par user.

- Priorite : Moyenne
- AC : export/anonymize/delete utilisateur disponibles cote admin API.

