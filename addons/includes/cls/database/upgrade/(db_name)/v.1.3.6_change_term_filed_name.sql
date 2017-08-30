ALTER TABLE `terms` DROP INDEX `termurl_unique`;

ALTER TABLE `terms` CHANGE `term_language` `language` CHAR(2) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `terms` CHANGE `term_type` `type` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'tag';
ALTER TABLE `terms` CHANGE `term_caller` `caller` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;
ALTER TABLE `terms` CHANGE `term_title` `title` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `terms` CHANGE `term_slug` `slug` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `terms` CHANGE `term_url` `url` VARCHAR(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `terms` CHANGE `term_desc` `desc` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;
ALTER TABLE `terms` CHANGE `term_meta` `meta` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;
ALTER TABLE `terms` CHANGE `term_parent` `parent` INT(10) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `terms` CHANGE `user_id` `user_id` INT(10) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `terms` CHANGE `term_status` `status` ENUM('enable','disable','expired','awaiting','filtered','blocked','spam','violence','pornography','other') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'awaiting';
ALTER TABLE `terms` CHANGE `term_count` `count` INT(10) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `terms` CHANGE `term_usercount` `usercount` INT(10) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `terms` CHANGE `date_modified` `date_modified` TIMESTAMP on update CURRENT_TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE `terms` ADD `createdate`    datetime DEFAULT CURRENT_TIMESTAMP;



ALTER TABLE `termusages` CHANGE `termusage_id` `related_id` BIGINT(20) UNSIGNED NOT NULL;
ALTER TABLE `termusages` CHANGE `termusage_foreign` `related` ENUM('posts','products','attachments','files','comments','users') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `termusages` CHANGE `termusage_order` `order` SMALLINT(5) UNSIGNED NULL DEFAULT NULL;

ALTER TABLE `termusages` ADD `status` ENUM('enable','disable','expired','awaiting','filtered','blocked','spam','violence','pornography','other', 'deleted') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'enable';
ALTER TABLE `termusages` ADD `createdate`    datetime DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `termusages` ADD `date_modified`  TIMESTAMP on update CURRENT_TIMESTAMP NULL DEFAULT NULL;

