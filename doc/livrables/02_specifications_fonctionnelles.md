# Specifications fonctionnelles

## 1. Contexte

Le produit "Wiki Builder API" permet de :

- consulter un catalogue multi-jeux (games, items, modifiers, categories)
- composer des builds
- comparer les performances
- gerer des drafts de jeux utilisateur
- publier un draft valide en jeu runtime

L'objectif fonctionnel principal est de fournir un parcours clair :

1. choisir un jeu
2. charger ou composer un build
3. comparer
4. publier ou reutiliser

## 2. Acteurs

- **Visiteur**
  - consulte la home
  - lit les routes publiques API
- **Utilisateur authentifie**
  - cree/edite ses drafts
  - gere son compte (export, anonymisation, suppression)
  - publie un draft
- **Admin**
  - gere les ressources globales (items, categories, archetypes, modifiers)
  - execute les actions RGPD admin sur un user cible

## 3. Perimetre fonctionnel

## 3.1 Inclus

- Auth web : inscription, connexion, deconnexion
- Dashboard : pilotage des drafts/publications
- Studio : workflow de creation d'un jeu via drafts
- Builder home : composition et comparaison de build
- API publique lecture
- API protegee ecriture
- RGPD compte utilisateur

## 3.2 Exclu (version actuelle)

- moderation communautaire avancee
- moteur de recommandation
- gestion complete de versions runtime publiees

## 4. Fonctionnalites detaillees

## F1 - Authentification web

- L'utilisateur peut creer un compte.
- L'utilisateur peut se connecter avec email/username + mot de passe.
- L'utilisateur connecte peut se deconnecter.

Critere d'acceptation :

- acces aux pages `/dashboard`, `/studio/games/create`, `/account` uniquement si connecte.

## F2 - Builder public

- Affiche les jeux disponibles.
- Permet de charger un build public.
- Permet d'ajouter des items dans 1 a 3 slots.
- Permet de configurer les modifiers compatibles.
- Affiche une matrice de comparaison si au moins 2 slots charges.

Critere d'acceptation :

- comparaison cachee tant que moins de 2 slots ont un item.

## F3 - Filtrage catalogue

- Recherche textuelle.
- Filtres slot/tier/sort.
- Filtres avances (score minimum, type mod, categories).

Critere d'acceptation :

- resultat filtre coherent avec pagination.

## F4 - Studio draft

- Creer un draft via categorie/template.
- Modifier fondation (name, slug, description, visibility).
- Modifier structure (slots, modifier types, flags).
- CRUD archetypes/categories/items/modifiers.
- Publier en jeu runtime.

Critere d'acceptation :

- publication refusee si regles de progression non valides.

## F5 - Dashboard

- Resume KPI (drafts, jeux publies, builds).
- Reprise d'un draft recent.
- Navigation rapide vers compte / confidentialite.

## F6 - Compte et RGPD

- Export des donnees personnelles.
- Anonymisation du compte (mot de passe requis).
- Suppression definitive du compte (mot de passe + confirmation).

## 5. Regles fonctionnelles majeures

- Un draft appartient a un seul utilisateur.
- Un draft publie ne peut pas etre supprime.
- La publication est transactionnelle : pas d'etat partiel en base runtime.
- Un build compare doit rester sur un meme jeu.
- L'affichage home doit rester lisible sur mobile/tablette/desktop.

## 6. Contraintes UX attendues

- Une page = une tache dominante.
- Action principale visible sans ambiguite.
- Labels explicites, pas de placeholder seul.
- Focus clavier visible et contrastes lisibles.
- Responsive sans perte d'information.

## 7. Definition of done (fonctionnelle)

- Tous les parcours principaux executes sans erreur bloquante.
- Les ecritures critiques sont protegees.
- Les messages d'erreur sont comprehensibles.
- Les documents de spec et UML sont fournis.

