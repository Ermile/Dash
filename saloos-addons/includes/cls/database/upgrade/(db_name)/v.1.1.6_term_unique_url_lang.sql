ALTER TABLE `terms` DROP INDEX `termurl_unique`;
ALTER TABLE `terms` ADD UNIQUE `termurl_unique` (`term_url`, `term_language`) USING BTREE;