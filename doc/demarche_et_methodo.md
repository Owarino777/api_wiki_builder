# Demarches de construction - Wiki Builder

## 1. Principe

Le projet doit rester data-driven.

Quand on ajoute un nouveau jeu, on doit surtout ajouter des donnees :
- `games`
- `archetypes`
- `items`
- `item_slots`
- `modifiers`
- `categories`
- compatibilites

Pas de remaniement du code pour gerer un slot ou un type specifique.

## 2. Ordre cible des migrations

1. `api_keys`
2. `users`
3. `games`
4. `archetypes`
5. `items`
6. `item_slots`
7. `modifiers`
8. `categories`
9. `builds`
10. `item_categories`
11. `item_modifiers`
12. `build_items`
13. `build_slots`

## 3. Points importants

- `slug` unique par jeu pour `items` et `modifiers`
- `external_ref` nullable pour supporter des catalogues heterogenes
- `item_slots` pour modeliser les emplacements internes
- `build_slots` pour modeliser l'equipement effectif
- `users` enrichi pour RGPD

## 4. Verification finale

- migration fraiche
- seed
- lecture catalogue
- detail item avec slots et modifiers compatibles
- creation build avec `items` + `slots`
- comparaison mono-jeu
- export / anonymisation utilisateur
