ALTER TABLE `posts` ADD `subdomain` varchar(100) NULL DEFAULT NULL AFTER `language`;

ALTER TABLE `posts` ADD INDEX `index_search_subdomain` (`subdomain`);
