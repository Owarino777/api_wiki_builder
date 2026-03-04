CREATE TABLE `api_keys` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `key` varchar(64) UNIQUE NOT NULL,
  `label` varchar(100),
  `is_active` boolean NOT NULL DEFAULT true,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `users` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `username` varchar(80) UNIQUE NOT NULL,
  `email` varchar(190) UNIQUE NOT NULL,
  `role` varchar(30) NOT NULL,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `games` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `slug` varchar(120) UNIQUE NOT NULL,
  `genre` varchar(80),
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `build_profiles` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `game_id` bigint NOT NULL,
  `name` varchar(120) NOT NULL,
  `description` text,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `equipment_items` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `game_id` bigint NOT NULL,
  `name` varchar(140) NOT NULL,
  `slot` varchar(60) NOT NULL,
  `rarity` varchar(40) NOT NULL,
  `tier_level` varchar(20),
  `score` int NOT NULL DEFAULT 0,
  `attack` int,
  `defense` int,
  `utility` int,
  `accuracy` int,
  `mobility` int,
  `metadata` json,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `equipment_modifiers` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `game_id` bigint NOT NULL,
  `name` varchar(140) NOT NULL,
  `modifier_type` varchar(60) NOT NULL,
  `description` text,
  `delta_score` int NOT NULL DEFAULT 0,
  `delta_attack` int,
  `delta_defense` int,
  `delta_utility` int,
  `delta_accuracy` int,
  `delta_mobility` int,
  `metadata` json,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `tags` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `label` varchar(80) UNIQUE NOT NULL,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `builds` (
  `id` bigint PRIMARY KEY AUTO_INCREMENT,
  `game_id` bigint NOT NULL,
  `user_id` bigint,
  `build_profile_id` bigint,
  `title` varchar(140) NOT NULL,
  `description` text,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `equipment_item_tags` (
  `equipment_item_id` bigint NOT NULL,
  `tag_id` bigint NOT NULL,
  PRIMARY KEY (`equipment_item_id`, `tag_id`)
);

CREATE TABLE `equipment_item_compatible_modifiers` (
  `equipment_item_id` bigint NOT NULL,
  `equipment_modifier_id` bigint NOT NULL,
  PRIMARY KEY (`equipment_item_id`, `equipment_modifier_id`)
);

CREATE TABLE `build_equipment_items` (
  `build_id` bigint NOT NULL,
  `equipment_item_id` bigint NOT NULL,
  `position` smallint,
  PRIMARY KEY (`build_id`, `equipment_item_id`)
);

CREATE TABLE `build_equipment_modifiers` (
  `build_id` bigint NOT NULL,
  `equipment_item_id` bigint NOT NULL,
  `equipment_modifier_id` bigint NOT NULL,
  PRIMARY KEY (`build_id`, `equipment_item_id`, `equipment_modifier_id`)
);

CREATE INDEX `build_profiles_index_0` ON `build_profiles` (`game_id`);

CREATE UNIQUE INDEX `build_profiles_index_1` ON `build_profiles` (`game_id`, `name`);

CREATE INDEX `equipment_items_index_2` ON `equipment_items` (`game_id`);

CREATE INDEX `equipment_items_index_3` ON `equipment_items` (`slot`);

CREATE INDEX `equipment_items_index_4` ON `equipment_items` (`rarity`);

CREATE INDEX `equipment_items_index_5` ON `equipment_items` (`game_id`, `score`);

CREATE UNIQUE INDEX `equipment_items_index_6` ON `equipment_items` (`game_id`, `name`);

CREATE INDEX `equipment_modifiers_index_7` ON `equipment_modifiers` (`game_id`);

CREATE INDEX `equipment_modifiers_index_8` ON `equipment_modifiers` (`modifier_type`);

CREATE INDEX `equipment_modifiers_index_9` ON `equipment_modifiers` (`game_id`, `modifier_type`);

CREATE UNIQUE INDEX `equipment_modifiers_index_10` ON `equipment_modifiers` (`game_id`, `name`);

CREATE INDEX `builds_index_11` ON `builds` (`game_id`);

CREATE INDEX `builds_index_12` ON `builds` (`user_id`);

CREATE INDEX `builds_index_13` ON `builds` (`build_profile_id`);

CREATE INDEX `equipment_item_tags_index_14` ON `equipment_item_tags` (`tag_id`);

CREATE INDEX `equipment_item_compatible_modifiers_index_15` ON `equipment_item_compatible_modifiers` (`equipment_modifier_id`);

CREATE INDEX `build_equipment_items_index_16` ON `build_equipment_items` (`equipment_item_id`);

CREATE INDEX `build_equipment_modifiers_index_17` ON `build_equipment_modifiers` (`equipment_modifier_id`);

CREATE INDEX `build_equipment_modifiers_index_18` ON `build_equipment_modifiers` (`equipment_item_id`);

ALTER TABLE `build_profiles` ADD FOREIGN KEY (`game_id`) REFERENCES `games` (`id`);

ALTER TABLE `equipment_items` ADD FOREIGN KEY (`game_id`) REFERENCES `games` (`id`);

ALTER TABLE `equipment_modifiers` ADD FOREIGN KEY (`game_id`) REFERENCES `games` (`id`);

ALTER TABLE `builds` ADD FOREIGN KEY (`game_id`) REFERENCES `games` (`id`);

ALTER TABLE `builds` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `builds` ADD FOREIGN KEY (`build_profile_id`) REFERENCES `build_profiles` (`id`);

ALTER TABLE `equipment_item_tags` ADD FOREIGN KEY (`equipment_item_id`) REFERENCES `equipment_items` (`id`);

ALTER TABLE `equipment_item_tags` ADD FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`);

ALTER TABLE `equipment_item_compatible_modifiers` ADD FOREIGN KEY (`equipment_item_id`) REFERENCES `equipment_items` (`id`);

ALTER TABLE `equipment_item_compatible_modifiers` ADD FOREIGN KEY (`equipment_modifier_id`) REFERENCES `equipment_modifiers` (`id`);

ALTER TABLE `build_equipment_items` ADD FOREIGN KEY (`build_id`) REFERENCES `builds` (`id`);

ALTER TABLE `build_equipment_items` ADD FOREIGN KEY (`equipment_item_id`) REFERENCES `equipment_items` (`id`);

ALTER TABLE `build_equipment_modifiers` ADD FOREIGN KEY (`build_id`) REFERENCES `builds` (`id`);

ALTER TABLE `build_equipment_modifiers` ADD FOREIGN KEY (`equipment_item_id`) REFERENCES `equipment_items` (`id`);

ALTER TABLE `build_equipment_modifiers` ADD FOREIGN KEY (`equipment_modifier_id`) REFERENCES `equipment_modifiers` (`id`);
