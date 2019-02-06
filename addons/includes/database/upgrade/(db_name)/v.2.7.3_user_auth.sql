CREATE TABLE `user_auth` (
`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
`user_id` int(10) UNSIGNED NULL,
`auth` char(32) NOT NULL,
`status` enum('enable','disalble','expire') NULL DEFAULT NULL,
`gateway` enum('android', 'ios', 'api') DEFAULT NULL,
`type` enum('guest', 'member') DEFAULT NULL,
`datecreated` timestamp DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`id`),
CONSTRAINT `user_auth_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `user_auth` ADD INDEX `index_search_auth` (`auth`);
ALTER TABLE `user_auth` ADD INDEX `index_search_status` (`status`);
ALTER TABLE `user_auth` ADD INDEX `index_search_user_id` (`user_id`);