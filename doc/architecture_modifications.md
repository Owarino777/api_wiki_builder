# Modifications d'architecture cible - Wiki Builder

## 1. Objet

Ce document centralise la cible retenue pour le refactor du projet.

Il sert de reference pour :
- le MPD
- le vocabulaire commun
- les regles metier
- la strategie RGPD

## 2. Decisions retenues

### 2.1 Deux niveaux de slots

Le champ `items.slot` et la table `item_slots` ne font pas doublon.

- `items.slot` = emplacement global dans le build ou le jeu
- `item_slots` = emplacements internes exposes par l'item pour des modificateurs

Exemple :
- `items.slot = weapon`
- `item_slots.slot_key = optic`, `magazine`, `stock`

### 2.2 Vocabulaire cible

Le projet adopte les noms suivants :
- `build_profiles` devient `archetypes`
- `equipment_items` devient `items`
- `equipment_modifiers` devient `modifiers`
- `tags` devient `categories`
- `equipment_item_tags` devient `item_categories`
- `equipment_item_compatible_modifiers` devient `item_modifiers`
- `build_equipment_items` devient `build_items`
- `build_equipment_modifiers` devient `build_slots`

### 2.3 Conception data-driven

L'ajout d'un nouveau jeu ne doit pas demander de modifier le code pour :
- declarer des slots specifiques
- declarer des types de modificateurs specifiques
- gerer un cas special par jeu

Le couple :
- `item_slots.slot_key`
- `item_slots.accepted_modifier_type`

permet de porter ces differences directement en base.

### 2.4 Classification et categories

Le projet retient `categories` comme classification lisible et exploitable par l'UI.

Exemples :
- `assault-rifle`
- `smg`
- `precision`
- `aggressive`

Si un futur besoin impose de vraies etiquettes libres separees, elles devront etre modelees comme un second concept, pas melangees a `categories`.

### 2.5 Unicite metier

Le couple `(game_id, name)` a ete abandonne pour `items` et `modifiers`.

La cible retient :
- `slug` unique par jeu
- `external_ref` nullable et indexe

Cela laisse plus de souplesse si deux objets partagent un meme nom d'affichage.

## 3. MPD cible complet

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
id              BIGINT UNSIGNED    PK AI
username        VARCHAR(80)        NOT NULL UNIQUE
email           VARCHAR(190)       NOT NULL UNIQUE
role            VARCHAR(30)        NOT NULL
first_name      VARCHAR(100)       NULL
last_name       VARCHAR(100)       NULL
display_name    VARCHAR(120)       NULL
phone           VARCHAR(30)        NULL
birth_date      DATE               NULL
country_code    CHAR(2)            NULL
city            VARCHAR(120)       NULL
postal_code     VARCHAR(20)        NULL
address_line_1  VARCHAR(190)       NULL
address_line_2  VARCHAR(190)       NULL
avatar_url      VARCHAR(255)       NULL
is_anonymized   TINYINT(1)         NOT NULL DEFAULT 0
anonymized_at   TIMESTAMP          NULL
created_at      TIMESTAMP          NULL
updated_at      TIMESTAMP          NULL

INDEX (role)
INDEX (is_anonymized)
INDEX (country_code)
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

### `archetypes`

```sql
id          BIGINT UNSIGNED    PK AI
game_id     BIGINT UNSIGNED    NOT NULL  FK -> games.id
name        VARCHAR(120)       NOT NULL
description TEXT               NULL
created_at  TIMESTAMP          NULL
updated_at  TIMESTAMP          NULL

UNIQUE (game_id, name)
INDEX  (game_id)
```

### `items`

```sql
id            BIGINT UNSIGNED    PK AI
game_id       BIGINT UNSIGNED    NOT NULL  FK -> games.id
name          VARCHAR(140)       NOT NULL
slug          VARCHAR(160)       NOT NULL
external_ref  VARCHAR(160)       NULL
slot          VARCHAR(60)        NOT NULL
rarity        VARCHAR(40)        NOT NULL
tier_level    VARCHAR(20)        NULL
score         INT                NOT NULL DEFAULT 0
attack        INT                NULL
defense       INT                NULL
utility       INT                NULL
accuracy      INT                NULL
mobility      INT                NULL
metadata      JSON               NULL
created_at    TIMESTAMP          NULL
updated_at    TIMESTAMP          NULL

UNIQUE (game_id, slug)
INDEX  (game_id)
INDEX  (game_id, external_ref)
INDEX  (slot)
INDEX  (rarity)
INDEX  (game_id, score)
```

### `item_slots`

```sql
id                      BIGINT UNSIGNED    PK AI
item_id                 BIGINT UNSIGNED    NOT NULL  FK -> items.id
slot_key                VARCHAR(60)        NOT NULL
slot_label              VARCHAR(80)        NOT NULL
accepted_modifier_type  VARCHAR(60)        NULL
sort_order              SMALLINT UNSIGNED  NOT NULL DEFAULT 1
is_required             TINYINT(1)         NOT NULL DEFAULT 0
metadata                JSON               NULL
created_at              TIMESTAMP          NULL
updated_at              TIMESTAMP          NULL

UNIQUE (item_id, slot_key)
INDEX  (item_id)
INDEX  (accepted_modifier_type)
INDEX  (item_id, sort_order)
```

### `modifiers`

```sql
id             BIGINT UNSIGNED    PK AI
game_id        BIGINT UNSIGNED    NOT NULL  FK -> games.id
name           VARCHAR(140)       NOT NULL
slug           VARCHAR(160)       NOT NULL
external_ref   VARCHAR(160)       NULL
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

UNIQUE (game_id, slug)
INDEX  (game_id)
INDEX  (game_id, external_ref)
INDEX  (modifier_type)
INDEX  (game_id, modifier_type)
```

### `categories`

```sql
id          BIGINT UNSIGNED    PK AI
label       VARCHAR(80)        NOT NULL UNIQUE
slug        VARCHAR(100)       NOT NULL UNIQUE
created_at  TIMESTAMP          NULL
updated_at  TIMESTAMP          NULL
```

### `builds`

```sql
id            BIGINT UNSIGNED    PK AI
game_id       BIGINT UNSIGNED    NOT NULL  FK -> games.id
user_id       BIGINT UNSIGNED    NULL      FK -> users.id
archetype_id  BIGINT UNSIGNED    NULL      FK -> archetypes.id
title         VARCHAR(140)       NOT NULL
description   TEXT               NULL
share_token   VARCHAR(64)        NULL UNIQUE
is_public     TINYINT(1)         NOT NULL DEFAULT 0
created_at    TIMESTAMP          NULL
updated_at    TIMESTAMP          NULL

INDEX (game_id)
INDEX (user_id)
INDEX (archetype_id)
INDEX (is_public)
```

### `item_categories`

```sql
item_id      BIGINT UNSIGNED    NOT NULL  FK -> items.id       ON DELETE CASCADE
category_id  BIGINT UNSIGNED    NOT NULL  FK -> categories.id  ON DELETE CASCADE

PRIMARY KEY (item_id, category_id)
INDEX (category_id)
```

### `item_modifiers`

```sql
item_id      BIGINT UNSIGNED    NOT NULL  FK -> items.id      ON DELETE CASCADE
modifier_id  BIGINT UNSIGNED    NOT NULL  FK -> modifiers.id  ON DELETE CASCADE

PRIMARY KEY (item_id, modifier_id)
INDEX (modifier_id)
```

### `build_items`

```sql
build_id  BIGINT UNSIGNED    NOT NULL  FK -> builds.id  ON DELETE CASCADE
item_id   BIGINT UNSIGNED    NOT NULL  FK -> items.id   ON DELETE CASCADE
position  SMALLINT UNSIGNED  NULL

PRIMARY KEY (build_id, item_id)
INDEX (item_id)
```

### `build_slots`

```sql
build_id      BIGINT UNSIGNED    NOT NULL  FK -> builds.id      ON DELETE CASCADE
item_id       BIGINT UNSIGNED    NOT NULL  FK -> items.id       ON DELETE CASCADE
item_slot_id  BIGINT UNSIGNED    NOT NULL  FK -> item_slots.id  ON DELETE CASCADE
modifier_id   BIGINT UNSIGNED    NOT NULL  FK -> modifiers.id   ON DELETE CASCADE

PRIMARY KEY (build_id, item_id, item_slot_id)
UNIQUE (build_id, item_id, modifier_id)
INDEX (modifier_id)
INDEX (item_id)
INDEX (item_slot_id)
```

## 4. Regles metier

- un build est mono-jeu
- un `archetype`, un `item` et un `modifier` doivent appartenir au meme jeu que le build
- un `modifier` ne peut etre equipe que sur un `item` present dans le build
- la compatibilite explicite passe par `item_modifiers`
- un `modifier` doit etre place via un `item_slot_id`
- `item_slot_id` doit appartenir a l'item vise
- `accepted_modifier_type` doit correspondre a `modifiers.modifier_type`, sauf si `NULL`
- un seul modificateur par slot dans un build
- la comparaison de builds est reservee a deux builds du meme jeu

## 5. Risques surveilles

Les points a surveiller restent :
- sens exact de `categories`
- normalisation du vocabulaire pour `slot`, `slot_key`, `modifier_type`
- contraintes mono-jeu qui reposent partiellement sur la validation metier
- justification de `build_items.position`

## 6. RGPD

La table `users` est volontairement plus riche pour permettre :
- export
- anonymisation
- suppression

Strategie cible :
- anonymiser les champs identifiants
- conserver la ligne `users` si besoin de garder l'historique fonctionnel
- ne pas casser les FK de `builds`
