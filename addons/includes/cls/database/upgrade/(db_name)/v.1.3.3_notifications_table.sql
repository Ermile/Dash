CREATE TABLE `notifications` (
`id`            bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
`user_id`       int(10) UNSIGNED NOT NULL,
`user_idsender` int(10) UNSIGNED NULL DEFAULT NULL,
`title`         varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
`content`       text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
`url`           varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
`read`          bit(1) NULL DEFAULT NULL,
`star`          bit(1) NULL DEFAULT NULL,
`status`        enum('enable','disable','expire', 'deleted') NULL DEFAULT 'enable',
`category`      smallint(5) NULL DEFAULT NULL,
`createdate`    datetime DEFAULT CURRENT_TIMESTAMP,
`senddate`      datetime NULL DEFAULT NULL,
`deliverdate`   datetime NULL DEFAULT NULL,
`expiredate`    datetime NULL DEFAULT NULL,
`readdate`      datetime NULL DEFAULT NULL,
`gateway`       enum('telegram','sms','system', 'email', 'call') NULL DEFAULT NULL,
`auto`          bit(1) NULL DEFAULT NULL,
`date_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
`desc`          text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
`meta`          mediumtext  CHARACTER SET utf8mb4 NULL DEFAULT NULL,
PRIMARY KEY (`id`),
CONSTRAINT `notifications_users_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `notifications_users_idsender` FOREIGN KEY (`user_idsender`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `notifications` ADD INDEX(`user_id`);