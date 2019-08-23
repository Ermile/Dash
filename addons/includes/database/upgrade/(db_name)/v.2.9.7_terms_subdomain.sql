ALTER TABLE `terms` ADD `subdomain` varchar(200) NULL DEFAULT NULL AFTER `type`;

ALTER TABLE `terms` ADD INDEX `index_search_subdomain` (`subdomain`);
ALTER TABLE `options` ADD INDEX `index_search_subdomain` (`subdomain`);

