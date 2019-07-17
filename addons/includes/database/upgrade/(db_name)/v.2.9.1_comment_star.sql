ALTER TABLE `comments` ADD `star` smallint(5) NULL DEFAULT NULL AFTER `plus`;

ALTER TABLE `comments` ADD INDEX `index_search_subdomain` (`subdomain`);
ALTER TABLE `comments` ADD INDEX `index_search_star` (`star`);
ALTER TABLE `comments` ADD INDEX `index_search_minus` (`minus`);
ALTER TABLE `comments` ADD INDEX `index_search_plus` (`plus`);
ALTER TABLE `comments` ADD INDEX `index_search_post_id` (`post_id`);
ALTER TABLE `comments` ADD INDEX `index_search_user_id` (`user_id`);
ALTER TABLE `comments` ADD INDEX `index_search_status` (`status`);
ALTER TABLE `comments` ADD INDEX `index_search_type` (`type`);

ALTER TABLE `comments` CHANGE `parent` `parent` bigint(20) unsigned NULL DEFAULT NULL;
