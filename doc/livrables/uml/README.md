# UML 2.5 - Sources PlantUML

## Fichiers

- `uml_01_use_case.puml` - cas d'utilisation visiteurs, utilisateurs, admin et client API.
- `uml_02_class_domain.puml` - modele domaine proche MCD/MPD avec jeux publies, builds, items et drafts Studio.
- `uml_03_sequence_draft_publish.puml` - sequence complete du parcours popup jusqu'a la publication.
- `uml_04_component_architecture.puml` - architecture Laravel, controllers, services, models, vues et stockage.
- `uml_05_deployment_docker.puml` - deploiement Docker Compose local et production.
- `uml_06_activity_draft_workflow.puml` - activite utilisateur du Studio et publication.
- `uml_07_routes_api.puml` - vue fonctionnelle des routes web, API publiques, API utilisateur et API admin.

## Sources analysees

- `routes/api.php` et `routes/web.php` pour la couverture des routes.
- `app/Http/Controllers`, `app/Services`, `app/Models` et `database/migrations` pour les responsabilites metier.
- `docker-compose.yml` et `docker-compose.prod.yml` pour le deploiement.
- `doc/schéma-mcd.png` et `doc/schéma_mpd.png` comme base visuelle des premiers diagrammes.

## Export (optionnel)

Avec PlantUML installe :

```powershell
plantuml .\doc\livrables\uml\uml_01_use_case.puml
```

Ou pour tous les fichiers :

```powershell
plantuml .\doc\livrables\uml\*.puml
```
