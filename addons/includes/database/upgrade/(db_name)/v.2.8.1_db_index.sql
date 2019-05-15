ALTER TABLE `users` ADD INDEX `index_search_permission` (`permission`);
ALTER TABLE `users` ADD INDEX `index_search_status` (`status`);


ALTER TABLE `posts` ADD INDEX `index_search_status` (`status`);
ALTER TABLE `posts` ADD INDEX `index_search_type` (`type`);
ALTER TABLE `posts` ADD INDEX `index_search_language` (`language`);
ALTER TABLE `posts` ADD INDEX `index_search_publishdate` (`publishdate`);
ALTER TABLE `posts` ADD INDEX `index_search_url` (`url`);
ALTER TABLE `posts` ADD INDEX `index_search_slug` (`slug`);


ALTER TABLE `terms` ADD INDEX `index_search_slug` (`slug`);
ALTER TABLE `terms` ADD INDEX `index_search_type` (`type`);

ALTER TABLE `transactions` ADD INDEX `index_search_condition` (`condition`);



