ALTER TABLE `users` ADD `theme` varchar(100) NULL DEFAULT NULL AFTER `sidebar`;

ALTER TABLE `users` CHANGE `gender` `gender` ENUM('male','female', 'company', 'rather not say') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;