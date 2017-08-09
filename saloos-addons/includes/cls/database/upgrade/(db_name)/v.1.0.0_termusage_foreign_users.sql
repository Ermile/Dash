ALTER TABLE `termusages` CHANGE `termusage_foreign` `termusage_foreign` ENUM('posts','products','attachments','files','comments','users') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `terms` ADD `term_status` ENUM('enable','disable','expired','awaiting','filtered','blocked','spam','violence','pornography','other') NOT NULL DEFAULT 'awaiting' AFTER `user_id`;

ALTER TABLE `terms` ADD `term_count` INT(10) UNSIGNED NULL AFTER `term_status`;
ALTER TABLE `logitems` ADD `logitem_type` VARCHAR(64) NULL AFTER `id`;

ALTER TABLE `logitems` ADD `logitem_caller` VARCHAR(500) NOT NULL AFTER `logitem_type`;

ALTER TABLE `terms` DROP INDEX `termurl_unique`;
-- ALTER TABLE `terms` ADD UNIQUE `termurl_unique` (`term_language`, `term_url`);
ALTER TABLE `terms` ADD UNIQUE `termurl_unique` (`term_url`);