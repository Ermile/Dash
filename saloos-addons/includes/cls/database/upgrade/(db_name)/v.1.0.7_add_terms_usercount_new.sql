ALTER TABLE `terms` ADD `term_usercount` INT(10) UNSIGNED NOT NULL DEFAULT 0 AFTER `term_count`;
DROP TRIGGER IF EXISTS `update_count`;