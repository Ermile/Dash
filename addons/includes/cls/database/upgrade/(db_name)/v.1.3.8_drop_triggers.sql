DROP TRIGGER `termusages_change_terms_count_usercount_on_delete`;
DROP TRIGGER `termusages_change_terms_count_usercount_on_update`;
DROP TRIGGER `termusages_change_terms_count_usercount_on_insert`;
ALTER TABLE `terms` CHANGE `usercount` `usercount` INT(10) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `termusages` DROP FOREIGN KEY  `termusages_terms_id`;
ALTER TABLE `termusages` DROP INDEX `term+type+object_unique`;

ALTER TABLE `termusages` ADD INDEX(`term_id`);
ALTER TABLE `termusages` ADD INDEX(`related_id`);
ALTER TABLE `termusages` ADD INDEX(`related`);
ALTER TABLE `termusages` ADD INDEX(`status`);