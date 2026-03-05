# WIKI BUILDER

---

## 1) Contexte & périmètre

Le projet consiste à développer une API REST avec Laravel permettant de gérer un catalogue multi-jeux (équipements/objets/armes, accessoires/modificateurs, tags/traits) et un module Builder pour assembler et comparer des configurations ("builds/loadouts").

L'API expose des endpoints permettant de lister, filtrer et trier les contenus d'un jeu sélectionné (toutes les données sont rattachées à un jeu ; un build ne peut contenir que des éléments appartenant au même jeu), ainsi que de créer, afficher, modifier, supprimer des builds via une agrégation simple de statistiques stockées (pas de simulation).

### Périmètre inclus

- Gestion d'un catalogue de jeux.
- Gestion de profils de build par jeu (optionnel : spécialiste COD, classe RPG, etc.).
- Gestion d'équipements par jeu (type/slot, rareté, tier/level, score, stats simples).
- Gestion d'accessoires/modificateurs par jeu (ex : attachments) + gestion des compatibilités équipement↔accessoire (many-to-many).
- Gestion de tags/traits associés aux équipements (many-to-many) pour faciliter recherche/filtrage.
- Gestion de builds : profil optionnel + sélection d'équipements + accessoires sélectionnés et comparaison simple (agrégats).
- Sécurisation : API Key stockée en base + contrôle de rôle (RBAC minimal) pour protéger les routes d'écriture.

### Hors périmètre

- Simulation avancée (DPS/TTK réels, perks détaillés, synergies complexes).
- Scraping/import automatique de données.

---

## 2) Cibles et utilisateurs

- **Gamer** : Consulte le catalogue, filtre et compare des builds.
- **Prof / évaluateur** : Lance l'API via Docker et teste les endpoints via Postman/cURL.
- **Administrateur** : Gère le référentiel via routes protégées (API Key + rôle).

---

## 3) Fonctionnalités

### Catalogue

- Lister/consulter les jeux.
- Lister/consulter les profils de build d'un jeu (optionnel).
- Lister/consulter les équipements d'un jeu avec filtres (slot, rareté, tier/level, score min/max, tag) + tri (nom, rareté, score) + pagination.
- Lister/consulter les tags et les équipements associés.
- Lister/consulter les accessoires/modificateurs d'un jeu (par type) et les équipements compatibles.

### Builder

- CRUD build : titre, profil optionnel, liste d'équipements, accessoires sélectionnés.
- Endpoint résumé : agrégation simple (ex : somme/moyenne) (`total_attack` / `defense` / `utility` / `score`).
- Endpoint comparaison : diff des agrégats entre deux builds.

### Règles métier (validées côté API)

- Un build est mono-jeu : les équipements/modificateurs/profil doivent appartenir au même jeu.
- Un modificateur ne peut être ajouté à un équipement que si la compatibilité existe (table de compatibilité).
- Résumé : somme stats équipements + somme deltas modificateurs.

---

## 4) Partie technique

- **Laravel** : Models, Migrations, Controllers, Routes.
- **MySQL** : schéma géré via migrations + seed dataset.
- **Resources** (JSON structuré) + **Form Requests** (validation/autorisation).
- **Middlewares** : Throttle + CheckApiKey (clé en base) + CheckRole (role sur user).
- **Docker Compose** : app + db, README de démarrage reproductible.

---

## 5) MCD (Modèle Conceptuel de Données)

> Relations nommées, pas de FK, pas de pivot

### Entités

| Entité | Attributs |
|---|---|
| `GAME` | game_id, name, slug, genre |
| `BUILD_PROFILE` *(optional per game)* | build_profile_id, name, description |
| `EQUIPMENT_ITEM` | equipment_item_id, name, slot, rarity, tier_level, score, attack, defense, utility, accuracy, mobility |
| `EQUIPMENT_MODIFIER` *(attachment/modifier)* | equipment_modifier_id, name, modifier_type, description, delta_score, delta_attack, delta_defense, delta_utility, delta_accuracy, delta_mobility |
| `TAG` | tag_id, label |
| `BUILD` | build_id, title, description, created_at |
| `API_KEY` | api_key_id, key, label |
| `USER` | user_id, username, email, role |

### Relations nommées + cardinalités

| Relation | Cardinalité |
|---|---|
| `OWNS_BUILD_PROFILES` | GAME (1,1) —— (0,N) BUILD_PROFILE |
| `OWNS_EQUIPMENT_ITEMS` | GAME (1,1) —— (0,N) EQUIPMENT_ITEM |
| `OWNS_EQUIPMENT_MODIFIERS` | GAME (1,1) —— (0,N) EQUIPMENT_MODIFIER |
| `OWNS_BUILDS` | GAME (1,1) —— (0,N) BUILD |
| `USES_BUILD_PROFILE` *(optional)* | BUILD_PROFILE (0,N) —— (0,1) BUILD |
| `TAGGED_WITH` *(many-to-many)* | EQUIPMENT_ITEM (0,N) —— (0,N) TAG |
| `COMPATIBLE_WITH` *(many-to-many)* | EQUIPMENT_ITEM (0,N) —— (0,N) EQUIPMENT_MODIFIER |
| `COMPOSED_OF` | BUILD (1,N) —— (0,N) EQUIPMENT_ITEM |
| `USES_MODIFIERS` *(many-to-many)* | BUILD (0,N) —— (0,N) EQUIPMENT_MODIFIER |
| `CREATED_BY` *(optional)* | USER (0,N) —— (0,1) BUILD |

### Notes

- Les tags sont globaux (réutilisables entre tous les jeux).
- Règle métier : un build ne peut référencer que des items/modificateurs/profils du même jeu (validation côté API).

---

## 6) MPD (Modèle Physique de Données)

> Tables MySQL, pivots, types, FK

### `api_keys`

```sql
id          BIGINT UNSIGNED    PK AI
key         VARCHAR(64)        NOT NULL UNIQUE
label       VARCHAR(100)       NULL
is_active   TINYINT(1)         NOT NULL DEFAULT 1
created_at  TIMESTAMP          NULL
updated_at  TIMESTAMP          NULL
```

### `users`

```sql
id          BIGINT UNSIGNED    PK AI
username    VARCHAR(80)        NOT NULL UNIQUE
email       VARCHAR(190)       NOT NULL UNIQUE
role        VARCHAR(30)        NOT NULL
created_at  TIMESTAMP          NULL
updated_at  TIMESTAMP          NULL
```

### `games`

```sql
id          BIGINT UNSIGNED    PK AI
name        VARCHAR(120)       NOT NULL
slug        VARCHAR(120)       NOT NULL UNIQUE
genre       VARCHAR(80)        NULL
created_at  TIMESTAMP          NULL
updated_at  TIMESTAMP          NULL
```

### `build_profiles`

```sql
id          BIGINT UNSIGNED    PK AI
game_id     BIGINT UNSIGNED    NOT NULL  FK → games.id
name        VARCHAR(120)       NOT NULL
description TEXT               NULL
created_at  TIMESTAMP          NULL
updated_at  TIMESTAMP          NULL

UNIQUE (game_id, name)
INDEX  (game_id)
```

### `equipment_items`

```sql
id          BIGINT UNSIGNED    PK AI
game_id     BIGINT UNSIGNED    NOT NULL  FK → games.id
name        VARCHAR(140)       NOT NULL
slot        VARCHAR(60)        NOT NULL
rarity      VARCHAR(40)        NOT NULL
tier_level  VARCHAR(20)        NULL
score       INT                NOT NULL DEFAULT 0
attack      INT                NULL
defense     INT                NULL
utility     INT                NULL
accuracy    INT                NULL
mobility    INT                NULL
metadata    JSON               NULL
created_at  TIMESTAMP          NULL
updated_at  TIMESTAMP          NULL

UNIQUE (game_id, name)
INDEX  (game_id)
INDEX  (slot)
INDEX  (rarity)
INDEX  (game_id, score)
```

### `equipment_modifiers`

```sql
id             BIGINT UNSIGNED    PK AI
game_id        BIGINT UNSIGNED    NOT NULL  FK → games.id
name           VARCHAR(140)       NOT NULL
modifier_type  VARCHAR(60)        NOT NULL
description    TEXT               NULL
delta_score    INT                NOT NULL DEFAULT 0
delta_attack   INT                NULL
delta_defense  INT                NULL
delta_utility  INT                NULL
delta_accuracy INT                NULL
delta_mobility INT                NULL
metadata       JSON               NULL
created_at     TIMESTAMP          NULL
updated_at     TIMESTAMP          NULL

UNIQUE (game_id, name)
INDEX  (game_id)
INDEX  (modifier_type)
INDEX  (game_id, modifier_type)
```

### `tags`

```sql
id          BIGINT UNSIGNED    PK AI
label       VARCHAR(80)        NOT NULL UNIQUE
created_at  TIMESTAMP          NULL
updated_at  TIMESTAMP          NULL
```

### `builds`

```sql
id                BIGINT UNSIGNED    PK AI
game_id           BIGINT UNSIGNED    NOT NULL  FK → games.id
user_id           BIGINT UNSIGNED    NULL      FK → users.id
build_profile_id  BIGINT UNSIGNED    NULL      FK → build_profiles.id
title             VARCHAR(140)       NOT NULL
description       TEXT               NULL
created_at        TIMESTAMP          NULL
updated_at        TIMESTAMP          NULL

INDEX (game_id)
INDEX (user_id)
INDEX (build_profile_id)
```

### `equipment_item_tags` *(pivot)*

```sql
equipment_item_id  BIGINT UNSIGNED    NOT NULL  FK → equipment_items.id  ON DELETE CASCADE
tag_id             BIGINT UNSIGNED    NOT NULL  FK → tags.id             ON DELETE CASCADE

PRIMARY KEY (equipment_item_id, tag_id)
INDEX (tag_id)
```

### `equipment_item_compatible_modifiers` *(pivot)*

```sql
equipment_item_id      BIGINT UNSIGNED    NOT NULL  FK → equipment_items.id     ON DELETE CASCADE
equipment_modifier_id  BIGINT UNSIGNED    NOT NULL  FK → equipment_modifiers.id ON DELETE CASCADE

PRIMARY KEY (equipment_item_id, equipment_modifier_id)
INDEX (equipment_modifier_id)
```

### `build_equipment_items` *(pivot)*

```sql
build_id           BIGINT UNSIGNED    NOT NULL  FK → builds.id          ON DELETE CASCADE
equipment_item_id  BIGINT UNSIGNED    NOT NULL  FK → equipment_items.id ON DELETE CASCADE
position           SMALLINT UNSIGNED  NULL

PRIMARY KEY (build_id, equipment_item_id)
INDEX (equipment_item_id)
```

### `build_equipment_modifiers` *(pivot)*

```sql
build_id               BIGINT UNSIGNED    NOT NULL  FK → builds.id             ON DELETE CASCADE
equipment_item_id      BIGINT UNSIGNED    NOT NULL  FK → equipment_items.id    ON DELETE CASCADE
equipment_modifier_id  BIGINT UNSIGNED    NOT NULL  FK → equipment_modifiers.id ON DELETE CASCADE

PRIMARY KEY (build_id, equipment_item_id, equipment_modifier_id)
INDEX (equipment_modifier_id)
INDEX (equipment_item_id)
```

### Contraintes métier (API)

- Un build est **mono-jeu** : les items/modificateurs/profils référencés doivent correspondre à `build.game_id`.
- Le modificateur doit être **compatible** avec l'item via la table `equipment_item_compatible_modifiers`.
- Le middleware **API Key** valide `api_keys.key` en base ; le middleware **rôle** utilise `users.role`.