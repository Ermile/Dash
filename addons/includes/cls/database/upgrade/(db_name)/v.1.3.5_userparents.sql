CREATE TABLE `userparents` (
`id`            bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
`user_id`       int(10) UNSIGNED NOT NULL,
`parent`       	int(10) UNSIGNED NOT NULL,
`creator`       int(10) UNSIGNED NULL DEFAULT NULL,
`level`      	smallint(5) NULL DEFAULT NULL,
`status`        enum('enable','disable','expire', 'deleted') NULL DEFAULT 'enable',
`title`       	enum('father','mother','sister','brother','grandfather','grandmother','aunt','husband of the aunt','uncle','boy','girl','spouse','stepmother','stepfather','neighbor','teacher','friend','boss','supervisor','child','grandson', 'custom') NULL DEFAULT NULL,
`othertitle`    varchar(255) NULL DEFAULT NULL,
`createdate`    datetime DEFAULT CURRENT_TIMESTAMP,
`date_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
`desc`          text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
`meta`          mediumtext  CHARACTER SET utf8mb4 NULL DEFAULT NULL,
PRIMARY KEY (`id`),
CONSTRAINT `userparents_users_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `userparents_creator` FOREIGN KEY (`creator`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `userparents_parent` FOREIGN KEY (`parent`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


