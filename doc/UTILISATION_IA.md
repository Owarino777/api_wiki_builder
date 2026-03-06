# Documentation de l'utilisation de l'IA — Wiki Builder API

## Outil utilisé

- **GitHub Copilot** (Claude) dans VS Code

## Utilisation de l'IA

L'IA a été utilisée comme **assistant de développement**, jamais comme générateur aveugle. Chaque suggestion a été relue, validée et adaptée manuellement.

### Ce que l'IA a aidé à faire

1. **Génération initiale du squelette** : L'IA a proposé un premier jet de la structure du projet basé sur le cahier des charges (`AI_INSTRUCTIONS.md`) et le MPD fourni. Ce squelette a ensuite été revu et corrigé manuellement.

2. **Correction de bugs** : 
   - Erreur MySQL "Identifier name too long" sur les FK des tables pivot (noms > 64 caractères). L'IA a identifié le problème et proposé l'utilisation du paramètre `indexName` dans les appels `constrained()`.

3. **Audit de conformité** : L'IA a comparé le code existant avec les documents de référence (`architecture.md`, `support_cours.md`, `wiki_game_builder.md`) pour identifier les écarts :
   - Migration monolithique → splittée en 12 fichiers (1 par table)
   - Ajout de `onDelete('cascade')` sur les FK des tables pivot
   - Utilisation de `unsignedSmallInteger` au lieu de `smallInteger` pour `position`
   - Ajout de l'enum `UserRole` (comme dans `architecture.md` du prof)

4. **Implémentation RGPD** : L'IA a aidé à concevoir le `RgpdController` avec les endpoints d'export, anonymisation et suppression.

5. **Gestion des exceptions** : Centralisation du rendu d'erreurs JSON dans `bootstrap/app.php`.

6. **Jeu de données réel** :
   - Recherche et validation d'un premier catalogue réel pour **Call of Duty: Black Ops III**
   - Préparation d'un seed BO3 normalisé pour l'application avec `source.page_url` et, quand vérifié proprement, `image_url` dans `metadata`
   - Conservation d'un second jeu synthétique pour démontrer le multi-jeux sans inventer un faux catalogue complet

7. **Documentation** : Rédaction du README et de ce document.

### Ce qui a été fait manuellement

- Relecture et validation de toutes les modifications
- Configuration de l'environnement local (MySQL, PHP, .env)
- Tests manuels des endpoints via curl
- Compréhension et application du MPD/MCD fourni
- Vérification de la cohérence entre le code et le barème d'évaluation
- Vérification manuelle de la cohérence entre les données BO3 seedées et les pages source retenues

### Pourquoi l'IA

L'IA a été utilisée pour :
- Gagner du temps sur le boilerplate (migrations, CRUD répétitif)
- Ne rien oublier dans le cahier des charges (audit automatique)
- Appliquer les bonnes pratiques Laravel 12 (middleware dans `bootstrap/app.php`, etc.)
- Accélérer la recherche documentaire sur les pages BO3 avant validation manuelle et intégration dans le seed

L'IA n'a **pas** été utilisée pour :
- Comprendre le sujet à ma place
- Inventer des fonctionnalités hors périmètre
- Remplacer la relecture et les tests

## Conclusion

L'utilisation de l'IA est assumée et documentée. Elle a servi d'accélérateur, pas de substitut à la compréhension du projet.
