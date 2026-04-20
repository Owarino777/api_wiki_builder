# WIKI BUILDER

## 1) Resume

Le projet est une API REST Laravel de **wiki builder multi-jeux**.

Le produit doit permettre :
- d'exposer les donnees d'un jeu sous forme de catalogue
- d'afficher le detail d'un item
- de construire un build avec contraintes reelles

## 2) Fonctionnalites cibles

### Catalogue

- jeux
- archetypes
- items
- modifiers
- categories

### Detail

Le detail d'un item doit exposer :
- ses stats
- ses categories
- ses slots internes (`item_slots`)
- ses modificateurs compatibles

### Builder

Le builder doit permettre :
- d'ajouter des items a un build
- d'equiper un modifier **via un slot precis**
- de resumer les stats
- de comparer deux builds du meme jeu
- de partager un build public

## 3) Nommage retenu

Le projet utilise maintenant les noms suivants :
- `archetypes`
- `items`
- `item_slots`
- `modifiers`
- `categories`
- `item_categories`
- `item_modifiers`
- `build_items`
- `build_slots`

## 4) Contraintes metier

- build mono-jeu
- compatibilite item/modifier obligatoire
- `build_slots` = "dans tel build, sur tel item, tel slot est occupe par tel modifier"
- `item_slots` ne fait pas doublon avec `items.slot`

## 5) RGPD

Le projet inclut volontairement une table `users` plus riche pour permettre :
- export
- anonymisation
- suppression

## 6) Reference

Le MPD cible complet est documente dans :
- `doc/architecture_modifications.md`
- `doc/diagrammes.md` — Diagrammes UML et base de donnees complets (cas d'utilisation, classes, sequences, etats-transitions, MCD Merise, MLD Merise, LDD SQL)

## 7) Jeux embarques

Le systeme est livre avec 5 jeux couvrant deux templates (FPS et RPG) :

| Jeu | Template | Items |
|---|---|---|
| Call of Duty: Black Ops 7 | FPS | 50 armes avec accessoires detailles |
| Battlefield 2042 | FPS | 6 armes avec optiques, canons, poignees |
| Valorant | FPS | 6 armes avec modifications tactiques |
| League of Legends | RPG | 6 champions avec objets et runes |
| New World | RPG | 6 armes avec gemmes et enchantements |

Les 4 nouveaux jeux sont peuples par des seeders offline (`database/seeders/`) heritant d'`AbstractGameSeeder`, qui factorise la logique d'import commune et fournit des helpers compacts (`item()`, `mod()`) pour definir les donnees.
