-- Wiki Builder SQL Dump
-- Generated: 2026-03-05 20:07:22
-- Database: wiki_game_builder

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `api_keys`;
CREATE TABLE `api_keys` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(64) NOT NULL,
  `label` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `api_keys_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `api_keys` VALUES ('1', 'dev-wiki-builder-key-1234567890abcdef', 'Postman Dev Key', '1', '2026-03-05 20:02:26', '2026-03-05 20:02:26');

DROP TABLE IF EXISTS `build_equipment_items`;
CREATE TABLE `build_equipment_items` (
  `build_id` bigint(20) unsigned NOT NULL,
  `equipment_item_id` bigint(20) unsigned NOT NULL,
  `position` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`build_id`,`equipment_item_id`),
  KEY `build_equipment_items_equipment_item_id_foreign` (`equipment_item_id`),
  CONSTRAINT `build_equipment_items_build_id_foreign` FOREIGN KEY (`build_id`) REFERENCES `builds` (`id`) ON DELETE CASCADE,
  CONSTRAINT `build_equipment_items_equipment_item_id_foreign` FOREIGN KEY (`equipment_item_id`) REFERENCES `equipment_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `build_equipment_items` VALUES ('1', '1', '1');
INSERT INTO `build_equipment_items` VALUES ('1', '2', '2');
INSERT INTO `build_equipment_items` VALUES ('1', '3', '3');
INSERT INTO `build_equipment_items` VALUES ('2', '11', '1');
INSERT INTO `build_equipment_items` VALUES ('2', '12', '2');
INSERT INTO `build_equipment_items` VALUES ('2', '13', '3');

DROP TABLE IF EXISTS `build_equipment_modifiers`;
CREATE TABLE `build_equipment_modifiers` (
  `build_id` bigint(20) unsigned NOT NULL,
  `equipment_item_id` bigint(20) unsigned NOT NULL,
  `equipment_modifier_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`build_id`,`equipment_item_id`,`equipment_modifier_id`),
  KEY `bem_item_fk` (`equipment_item_id`),
  KEY `bem_modifier_fk` (`equipment_modifier_id`),
  CONSTRAINT `bem_item_fk` FOREIGN KEY (`equipment_item_id`) REFERENCES `equipment_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bem_modifier_fk` FOREIGN KEY (`equipment_modifier_id`) REFERENCES `equipment_modifiers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `build_equipment_modifiers_build_id_foreign` FOREIGN KEY (`build_id`) REFERENCES `builds` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `build_equipment_modifiers` VALUES ('1', '1', '1');
INSERT INTO `build_equipment_modifiers` VALUES ('1', '2', '2');
INSERT INTO `build_equipment_modifiers` VALUES ('1', '3', '3');
INSERT INTO `build_equipment_modifiers` VALUES ('2', '11', '11');
INSERT INTO `build_equipment_modifiers` VALUES ('2', '12', '12');
INSERT INTO `build_equipment_modifiers` VALUES ('2', '13', '13');

DROP TABLE IF EXISTS `build_profiles`;
CREATE TABLE `build_profiles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `game_id` bigint(20) unsigned NOT NULL,
  `name` varchar(120) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `build_profiles_game_id_name_unique` (`game_id`,`name`),
  CONSTRAINT `build_profiles_game_id_foreign` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `build_profiles` VALUES ('1', '1', 'Burst Assassin', 'High damage loadout with mobility focus.', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `build_profiles` VALUES ('2', '1', 'Tank Vanguard', 'Defensive setup for frontline survivability.', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `build_profiles` VALUES ('3', '1', 'Hybrid Scout', 'Balanced setup with flexible utility.', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `build_profiles` VALUES ('4', '2', 'Precision Sniper', 'Long-range profile with accuracy priority.', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `build_profiles` VALUES ('5', '2', 'Close Quarters', 'Aggressive profile for short distance combat.', '2026-03-05 20:02:26', '2026-03-05 20:02:26');

DROP TABLE IF EXISTS `builds`;
CREATE TABLE `builds` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `game_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `build_profile_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(140) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `builds_game_id_foreign` (`game_id`),
  KEY `builds_user_id_index` (`user_id`),
  KEY `builds_build_profile_id_index` (`build_profile_id`),
  CONSTRAINT `builds_build_profile_id_foreign` FOREIGN KEY (`build_profile_id`) REFERENCES `build_profiles` (`id`) ON DELETE SET NULL,
  CONSTRAINT `builds_game_id_foreign` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE,
  CONSTRAINT `builds_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `builds` VALUES ('1', '1', '2', '1', 'Aetherfall Burst Setup', 'Seeded sample build for summary checks.', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `builds` VALUES ('2', '2', '1', '4', 'Steel Horizon Sniper Setup', 'Seeded sample build for compare endpoint.', '2026-03-05 20:02:26', '2026-03-05 20:02:26');

DROP TABLE IF EXISTS `equipment_item_compatible_modifiers`;
CREATE TABLE `equipment_item_compatible_modifiers` (
  `equipment_item_id` bigint(20) unsigned NOT NULL,
  `equipment_modifier_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`equipment_item_id`,`equipment_modifier_id`),
  KEY `eicm_modifier_fk` (`equipment_modifier_id`),
  CONSTRAINT `eicm_item_fk` FOREIGN KEY (`equipment_item_id`) REFERENCES `equipment_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `eicm_modifier_fk` FOREIGN KEY (`equipment_modifier_id`) REFERENCES `equipment_modifiers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `equipment_item_compatible_modifiers` VALUES ('1', '1');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('1', '4');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('2', '2');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('2', '5');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('3', '3');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('3', '6');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('4', '4');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('4', '7');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('5', '5');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('5', '8');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('6', '6');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('6', '9');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('7', '7');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('7', '10');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('8', '1');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('8', '8');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('9', '2');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('9', '9');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('10', '3');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('10', '10');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('11', '11');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('11', '14');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('12', '12');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('12', '15');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('13', '13');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('13', '16');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('14', '14');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('14', '17');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('15', '15');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('15', '18');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('16', '16');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('16', '19');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('17', '17');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('17', '20');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('18', '11');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('18', '18');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('19', '12');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('19', '19');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('20', '13');
INSERT INTO `equipment_item_compatible_modifiers` VALUES ('20', '20');

DROP TABLE IF EXISTS `equipment_item_tags`;
CREATE TABLE `equipment_item_tags` (
  `equipment_item_id` bigint(20) unsigned NOT NULL,
  `tag_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`equipment_item_id`,`tag_id`),
  KEY `equipment_item_tags_tag_id_foreign` (`tag_id`),
  CONSTRAINT `equipment_item_tags_equipment_item_id_foreign` FOREIGN KEY (`equipment_item_id`) REFERENCES `equipment_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `equipment_item_tags_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `equipment_item_tags` VALUES ('1', '1');
INSERT INTO `equipment_item_tags` VALUES ('1', '6');
INSERT INTO `equipment_item_tags` VALUES ('2', '2');
INSERT INTO `equipment_item_tags` VALUES ('2', '4');
INSERT INTO `equipment_item_tags` VALUES ('3', '3');
INSERT INTO `equipment_item_tags` VALUES ('3', '8');
INSERT INTO `equipment_item_tags` VALUES ('4', '1');
INSERT INTO `equipment_item_tags` VALUES ('4', '7');
INSERT INTO `equipment_item_tags` VALUES ('5', '2');
INSERT INTO `equipment_item_tags` VALUES ('5', '5');
INSERT INTO `equipment_item_tags` VALUES ('6', '3');
INSERT INTO `equipment_item_tags` VALUES ('6', '6');
INSERT INTO `equipment_item_tags` VALUES ('7', '4');
INSERT INTO `equipment_item_tags` VALUES ('7', '7');
INSERT INTO `equipment_item_tags` VALUES ('8', '5');
INSERT INTO `equipment_item_tags` VALUES ('8', '8');
INSERT INTO `equipment_item_tags` VALUES ('9', '1');
INSERT INTO `equipment_item_tags` VALUES ('9', '6');
INSERT INTO `equipment_item_tags` VALUES ('10', '2');
INSERT INTO `equipment_item_tags` VALUES ('10', '4');
INSERT INTO `equipment_item_tags` VALUES ('11', '3');
INSERT INTO `equipment_item_tags` VALUES ('11', '8');
INSERT INTO `equipment_item_tags` VALUES ('12', '1');
INSERT INTO `equipment_item_tags` VALUES ('12', '7');
INSERT INTO `equipment_item_tags` VALUES ('13', '2');
INSERT INTO `equipment_item_tags` VALUES ('13', '5');
INSERT INTO `equipment_item_tags` VALUES ('14', '3');
INSERT INTO `equipment_item_tags` VALUES ('14', '6');
INSERT INTO `equipment_item_tags` VALUES ('15', '4');
INSERT INTO `equipment_item_tags` VALUES ('15', '7');
INSERT INTO `equipment_item_tags` VALUES ('16', '5');
INSERT INTO `equipment_item_tags` VALUES ('16', '8');
INSERT INTO `equipment_item_tags` VALUES ('17', '1');
INSERT INTO `equipment_item_tags` VALUES ('17', '6');
INSERT INTO `equipment_item_tags` VALUES ('18', '2');
INSERT INTO `equipment_item_tags` VALUES ('18', '4');
INSERT INTO `equipment_item_tags` VALUES ('19', '3');
INSERT INTO `equipment_item_tags` VALUES ('19', '8');
INSERT INTO `equipment_item_tags` VALUES ('20', '1');
INSERT INTO `equipment_item_tags` VALUES ('20', '7');

DROP TABLE IF EXISTS `equipment_items`;
CREATE TABLE `equipment_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `game_id` bigint(20) unsigned NOT NULL,
  `name` varchar(140) NOT NULL,
  `slot` varchar(60) NOT NULL,
  `rarity` varchar(40) NOT NULL,
  `tier_level` varchar(20) DEFAULT NULL,
  `score` int(11) NOT NULL DEFAULT 0,
  `attack` int(11) DEFAULT NULL,
  `defense` int(11) DEFAULT NULL,
  `utility` int(11) DEFAULT NULL,
  `accuracy` int(11) DEFAULT NULL,
  `mobility` int(11) DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `equipment_items_game_id_name_unique` (`game_id`,`name`),
  KEY `equipment_items_slot_index` (`slot`),
  KEY `equipment_items_rarity_index` (`rarity`),
  KEY `equipment_items_game_id_score_index` (`game_id`,`score`),
  CONSTRAINT `equipment_items_game_id_foreign` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `equipment_items` VALUES ('1', '1', 'AE Item 1', 'helmet', 'rare', 'T2', '53', NULL, NULL, NULL, NULL, '7', '{\"seed\":\"equipment_items\",\"index\":1}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_items` VALUES ('2', '1', 'AE Item 2', 'armor', 'epic', 'T3', '56', '12', NULL, NULL, NULL, NULL, '{\"seed\":\"equipment_items\",\"index\":2}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_items` VALUES ('3', '1', 'AE Item 3', 'gloves', 'legendary', 'T4', '59', NULL, '11', NULL, NULL, '9', '{\"seed\":\"equipment_items\",\"index\":3}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_items` VALUES ('4', '1', 'AE Item 4', 'boots', 'common', 'T5', '62', '14', NULL, '9', NULL, NULL, '{\"seed\":\"equipment_items\",\"index\":4}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_items` VALUES ('5', '1', 'AE Item 5', 'weapon', 'rare', 'T1', '65', NULL, NULL, NULL, '9', '11', '{\"seed\":\"equipment_items\",\"index\":5}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_items` VALUES ('6', '1', 'AE Item 6', 'helmet', 'epic', 'T2', '68', '16', '14', NULL, NULL, NULL, '{\"seed\":\"equipment_items\",\"index\":6}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_items` VALUES ('7', '1', 'AE Item 7', 'armor', 'legendary', 'T3', '71', NULL, NULL, NULL, NULL, '13', '{\"seed\":\"equipment_items\",\"index\":7}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_items` VALUES ('8', '1', 'AE Item 8', 'gloves', 'common', 'T4', '74', '18', NULL, '13', NULL, NULL, '{\"seed\":\"equipment_items\",\"index\":8}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_items` VALUES ('9', '1', 'AE Item 9', 'boots', 'rare', 'T5', '77', NULL, '17', NULL, NULL, '15', '{\"seed\":\"equipment_items\",\"index\":9}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_items` VALUES ('10', '1', 'AE Item 10', 'weapon', 'epic', 'T1', '80', '20', NULL, NULL, '14', NULL, '{\"seed\":\"equipment_items\",\"index\":10}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_items` VALUES ('11', '2', 'ST Item 1', 'helmet', 'rare', 'T2', '53', NULL, NULL, NULL, NULL, '7', '{\"seed\":\"equipment_items\",\"index\":1}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_items` VALUES ('12', '2', 'ST Item 2', 'armor', 'epic', 'T3', '56', '12', NULL, NULL, NULL, NULL, '{\"seed\":\"equipment_items\",\"index\":2}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_items` VALUES ('13', '2', 'ST Item 3', 'gloves', 'legendary', 'T4', '59', NULL, '11', NULL, NULL, '9', '{\"seed\":\"equipment_items\",\"index\":3}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_items` VALUES ('14', '2', 'ST Item 4', 'boots', 'common', 'T5', '62', '14', NULL, '9', NULL, NULL, '{\"seed\":\"equipment_items\",\"index\":4}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_items` VALUES ('15', '2', 'ST Item 5', 'weapon', 'rare', 'T1', '65', NULL, NULL, NULL, '9', '11', '{\"seed\":\"equipment_items\",\"index\":5}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_items` VALUES ('16', '2', 'ST Item 6', 'helmet', 'epic', 'T2', '68', '16', '14', NULL, NULL, NULL, '{\"seed\":\"equipment_items\",\"index\":6}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_items` VALUES ('17', '2', 'ST Item 7', 'armor', 'legendary', 'T3', '71', NULL, NULL, NULL, NULL, '13', '{\"seed\":\"equipment_items\",\"index\":7}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_items` VALUES ('18', '2', 'ST Item 8', 'gloves', 'common', 'T4', '74', '18', NULL, '13', NULL, NULL, '{\"seed\":\"equipment_items\",\"index\":8}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_items` VALUES ('19', '2', 'ST Item 9', 'boots', 'rare', 'T5', '77', NULL, '17', NULL, NULL, '15', '{\"seed\":\"equipment_items\",\"index\":9}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_items` VALUES ('20', '2', 'ST Item 10', 'weapon', 'epic', 'T1', '80', '20', NULL, NULL, '14', NULL, '{\"seed\":\"equipment_items\",\"index\":10}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');

DROP TABLE IF EXISTS `equipment_modifiers`;
CREATE TABLE `equipment_modifiers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `game_id` bigint(20) unsigned NOT NULL,
  `name` varchar(140) NOT NULL,
  `modifier_type` varchar(60) NOT NULL,
  `description` text DEFAULT NULL,
  `delta_score` int(11) NOT NULL DEFAULT 0,
  `delta_attack` int(11) DEFAULT NULL,
  `delta_defense` int(11) DEFAULT NULL,
  `delta_utility` int(11) DEFAULT NULL,
  `delta_accuracy` int(11) DEFAULT NULL,
  `delta_mobility` int(11) DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `equipment_modifiers_game_id_name_unique` (`game_id`,`name`),
  KEY `equipment_modifiers_modifier_type_index` (`modifier_type`),
  KEY `equipment_modifiers_game_id_modifier_type_index` (`game_id`,`modifier_type`),
  CONSTRAINT `equipment_modifiers_game_id_foreign` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `equipment_modifiers` VALUES ('1', '1', 'AE Mod 1', 'defense', 'Generated modifier 1 for aetherfall.', '-1', NULL, NULL, NULL, NULL, '2', '{\"seed\":\"equipment_modifiers\",\"index\":1}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_modifiers` VALUES ('2', '1', 'AE Mod 2', 'mobility', 'Generated modifier 2 for aetherfall.', '4', '2', NULL, NULL, NULL, NULL, '{\"seed\":\"equipment_modifiers\",\"index\":2}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_modifiers` VALUES ('3', '1', 'AE Mod 3', 'utility', 'Generated modifier 3 for aetherfall.', '-1', NULL, '2', NULL, NULL, '2', '{\"seed\":\"equipment_modifiers\",\"index\":3}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_modifiers` VALUES ('4', '1', 'AE Mod 4', 'offense', 'Generated modifier 4 for aetherfall.', '4', '2', NULL, '1', NULL, NULL, '{\"seed\":\"equipment_modifiers\",\"index\":4}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_modifiers` VALUES ('5', '1', 'AE Mod 5', 'defense', 'Generated modifier 5 for aetherfall.', '-1', NULL, NULL, NULL, '1', '2', '{\"seed\":\"equipment_modifiers\",\"index\":5}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_modifiers` VALUES ('6', '1', 'AE Mod 6', 'mobility', 'Generated modifier 6 for aetherfall.', '4', '2', '2', NULL, NULL, NULL, '{\"seed\":\"equipment_modifiers\",\"index\":6}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_modifiers` VALUES ('7', '1', 'AE Mod 7', 'utility', 'Generated modifier 7 for aetherfall.', '-1', NULL, NULL, NULL, NULL, '2', '{\"seed\":\"equipment_modifiers\",\"index\":7}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_modifiers` VALUES ('8', '1', 'AE Mod 8', 'offense', 'Generated modifier 8 for aetherfall.', '4', '2', NULL, '1', NULL, NULL, '{\"seed\":\"equipment_modifiers\",\"index\":8}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_modifiers` VALUES ('9', '1', 'AE Mod 9', 'defense', 'Generated modifier 9 for aetherfall.', '-1', NULL, '2', NULL, NULL, '2', '{\"seed\":\"equipment_modifiers\",\"index\":9}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_modifiers` VALUES ('10', '1', 'AE Mod 10', 'mobility', 'Generated modifier 10 for aetherfall.', '4', '2', NULL, NULL, '1', NULL, '{\"seed\":\"equipment_modifiers\",\"index\":10}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_modifiers` VALUES ('11', '2', 'ST Mod 1', 'defense', 'Generated modifier 1 for steel-horizon.', '-1', NULL, NULL, NULL, NULL, '2', '{\"seed\":\"equipment_modifiers\",\"index\":1}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_modifiers` VALUES ('12', '2', 'ST Mod 2', 'mobility', 'Generated modifier 2 for steel-horizon.', '4', '2', NULL, NULL, NULL, NULL, '{\"seed\":\"equipment_modifiers\",\"index\":2}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_modifiers` VALUES ('13', '2', 'ST Mod 3', 'utility', 'Generated modifier 3 for steel-horizon.', '-1', NULL, '2', NULL, NULL, '2', '{\"seed\":\"equipment_modifiers\",\"index\":3}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_modifiers` VALUES ('14', '2', 'ST Mod 4', 'offense', 'Generated modifier 4 for steel-horizon.', '4', '2', NULL, '1', NULL, NULL, '{\"seed\":\"equipment_modifiers\",\"index\":4}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_modifiers` VALUES ('15', '2', 'ST Mod 5', 'defense', 'Generated modifier 5 for steel-horizon.', '-1', NULL, NULL, NULL, '1', '2', '{\"seed\":\"equipment_modifiers\",\"index\":5}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_modifiers` VALUES ('16', '2', 'ST Mod 6', 'mobility', 'Generated modifier 6 for steel-horizon.', '4', '2', '2', NULL, NULL, NULL, '{\"seed\":\"equipment_modifiers\",\"index\":6}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_modifiers` VALUES ('17', '2', 'ST Mod 7', 'utility', 'Generated modifier 7 for steel-horizon.', '-1', NULL, NULL, NULL, NULL, '2', '{\"seed\":\"equipment_modifiers\",\"index\":7}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_modifiers` VALUES ('18', '2', 'ST Mod 8', 'offense', 'Generated modifier 8 for steel-horizon.', '4', '2', NULL, '1', NULL, NULL, '{\"seed\":\"equipment_modifiers\",\"index\":8}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_modifiers` VALUES ('19', '2', 'ST Mod 9', 'defense', 'Generated modifier 9 for steel-horizon.', '-1', NULL, '2', NULL, NULL, '2', '{\"seed\":\"equipment_modifiers\",\"index\":9}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `equipment_modifiers` VALUES ('20', '2', 'ST Mod 10', 'mobility', 'Generated modifier 10 for steel-horizon.', '4', '2', NULL, NULL, '1', NULL, '{\"seed\":\"equipment_modifiers\",\"index\":10}', '2026-03-05 20:02:26', '2026-03-05 20:02:26');

DROP TABLE IF EXISTS `games`;
CREATE TABLE `games` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `genre` varchar(80) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `games_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `games` VALUES ('1', 'Aetherfall', 'aetherfall', 'Action RPG', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `games` VALUES ('2', 'Steel Horizon', 'steel-horizon', 'Tactical Shooter', '2026-03-05 20:02:26', '2026-03-05 20:02:26');

DROP TABLE IF EXISTS `tags`;
CREATE TABLE `tags` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(80) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tags_label_unique` (`label`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tags` VALUES ('1', 'pve', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `tags` VALUES ('2', 'pvp', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `tags` VALUES ('3', 'starter', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `tags` VALUES ('4', 'endgame', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `tags` VALUES ('5', 'tank', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `tags` VALUES ('6', 'damage', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `tags` VALUES ('7', 'support', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `tags` VALUES ('8', 'mobility', '2026-03-05 20:02:26', '2026-03-05 20:02:26');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(80) NOT NULL,
  `email` varchar(190) NOT NULL,
  `role` varchar(30) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` VALUES ('1', 'admin', 'admin@wikibuilder.local', 'admin', '2026-03-05 20:02:26', '2026-03-05 20:02:26');
INSERT INTO `users` VALUES ('2', 'player1', 'player1@wikibuilder.local', 'user', '2026-03-05 20:02:26', '2026-03-05 20:02:26');

SET FOREIGN_KEY_CHECKS = 1;
