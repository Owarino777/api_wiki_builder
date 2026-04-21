# UML 2.5 - Sources PlantUML

## Fichiers

- `uml_01_use_case.puml` - cas d'utilisation visiteurs, utilisateurs, admin et client API. Inclut UC_TEMPLATES (game-categories), UC_MY_GAMES (/my/games, jeux prives + publics du proprio) et UC_BUILD_WRITE (creation de builds via API KEY). Note de visibilite : la page d'accueil n'affiche que les jeux publics.
- `uml_02_class_domain.puml` - modele domaine complet avec champs RGPD complets sur User, noms des tables physiques (archetypes, categories, items, modifiers, item_slots) et note sur le systeme de visibilite des jeux.
- `uml_03_sequence_draft_publish.puml` - sequence complete du parcours popup jusqu'a la publication.
- `uml_04_component_architecture.puml` - architecture Laravel detaillee : controllers, services (dont UserPrivacyService), middlewares (throttle, cache.api, check.api.key, require.request.user, check.role), distinction session auth (Studio) vs API KEY auth.
- `uml_05_deployment_docker.puml` - deploiement Docker Compose local et production.
- `uml_06_activity_draft_workflow.puml` - activite utilisateur du Studio et publication.
- `uml_07_routes_api.puml` - vue fonctionnelle exhaustive des routes web, API publiques (dont /game-categories, /builds/share, /builds/{id}/summary), API utilisateur (dont /my/games, builds write), API admin (dont RGPD users) et Studio interne (session auth).

## Sources analysees

- `routes/api.php` et `routes/web.php` pour la couverture exhaustive des routes.
- `app/Http/Controllers`, `app/Services`, `app/Models` pour les responsabilites metier.
- `database/migrations` (27 fichiers) pour les schemas exacts des tables.
- `docker-compose.yml` et `docker-compose.prod.yml` pour le deploiement.

## Export (optionnel)

Avec PlantUML installe :

```powershell
plantuml .\doc\livrables\uml\uml_01_use_case.puml
```

Ou pour tous les fichiers :

```powershell
plantuml .\doc\livrables\uml\*.puml
```
