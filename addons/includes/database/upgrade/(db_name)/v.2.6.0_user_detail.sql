
ALTER TABLE `users` ADD `father` varchar(100) DEFAULT NULL;
ALTER TABLE `users` ADD `nationalcode` varchar(50) DEFAULT NULL;
ALTER TABLE `users` ADD `nationality` varchar(5) DEFAULT NULL;
ALTER TABLE `users` ADD `pasportcode` varchar(50) DEFAULT NULL;
ALTER TABLE `users` ADD `pasportdate` varchar(20) DEFAULT NULL;
ALTER TABLE `users` ADD `marital` enum('single','married') DEFAULT NULL;
ALTER TABLE `users` ADD `foreign` bit(1) DEFAULT NULL;
ALTER TABLE `users` ADD `phone` varchar(1000) DEFAULT NULL;
ALTER TABLE `users` ADD `detail` text CHARACTER SET utf8mb4;







CREATE TABLE `userdetail` (
`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
`user_id` int(10) UNSIGNED NOT NULL,
`creator` int(10) UNSIGNED NULL,
`subdomain` varchar(200) CHARACTER SET utf8mb4 DEFAULT NULL,
`status` enum('enable','disable','filter','spam','delete') NULL DEFAULT 'enable',
`text` text CHARACTER SET utf8mb4 DEFAULT NULL,
`datecreated` timestamp DEFAULT CURRENT_TIMESTAMP,
`datemodified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`id`),
CONSTRAINT `userdetail_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
CONSTRAINT `userdetail_creator` FOREIGN KEY (`creator`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
