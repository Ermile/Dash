CREATE TABLE `user_telegram` (
`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
`user_id` int(10) UNSIGNED NOT NULL,
`chatid` bigint(20) NOT NULL,
`firstname` varchar(200) CHARACTER SET utf8mb4 DEFAULT NULL,
`lastname` varchar(200) CHARACTER SET utf8mb4 DEFAULT NULL,
`username` varchar(200) CHARACTER SET utf8mb4 DEFAULT NULL,
`language` char(2) CHARACTER SET utf8mb4 DEFAULT NULL,
`status` enum('active','deactive','spam','bot','block','unreachable','unknown','filter','awaiting','inline','callback') NULL DEFAULT NULL,
`lastupdate` timestamp  NULL DEFAULT NULL,
`datecreated` timestamp DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`id`),
CONSTRAINT `user_tg_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




CREATE TABLE `user_android` (
`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
`user_id` int(10) UNSIGNED NOT NULL,
`uniquecode` char(32) NOT NULL,
`osversion` varchar(200) DEFAULT NULL,
`version` varchar(200) DEFAULT NULL,
`serial` varchar(200) DEFAULT NULL,
`model` varchar(200) DEFAULT NULL,
`manufacturer` varchar(200) DEFAULT NULL,
`language` char(2) CHARACTER SET utf8mb4 DEFAULT NULL,
`status` enum('active','deactive','spam','bot','block','unreachable','unknown','filter','awaiting') NULL DEFAULT NULL,
`meta` text CHARACTER SET utf8mb4 DEFAULT NULL,
`lastupdate` timestamp NULL DEFAULT NULL,
`datecreated` timestamp DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`id`),
CONSTRAINT `user_android_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



INSERT INTO user_telegram
(
	`user_id`,
	`chatid`,
	`firstname`,
	`lastname`,
	`username`,
	`language`,
	`status`,
	`lastupdate`
)
SELECT
	users.id,
	users.chatid,
	users.firstname,
	users.lastname,
	users.tgusername,
	users.language,
	users.tgstatus,
	users.tg_lastupdate
FROM
	users
WHERE users.chatid IS NOT NULL;