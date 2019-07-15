CREATE TABLE IF NOT EXISTS `files` (
`id` 			int(10) unsigned NOT NULL AUTO_INCREMENT,
`user_id` 		int(10) unsigned DEFAULT NULL,
`md5` 			char(32) NULL DEFAULT NULL,
`filename` 		varchar(500) CHARACTER SET utf8mb4 NULL DEFAULT NULL,
`title` 		varchar(500) CHARACTER SET utf8mb4 NULL DEFAULT NULL,
`desc` 			text CHARACTER SET utf8mb4,
`useage` 		varchar(200) CHARACTER SET utf8mb4 NULL DEFAULT NULL,
`type` 			varchar(200) CHARACTER SET utf8mb4 NULL DEFAULT NULL,
`mime` 			varchar(200) CHARACTER SET utf8mb4 NULL DEFAULT NULL,
`ext` 			varchar(100) CHARACTER SET utf8mb4 NULL DEFAULT NULL,
`folder` 		varchar(100) CHARACTER SET utf8mb4 NULL DEFAULT NULL,
`url` 			varchar(2000) CHARACTER SET utf8mb4 NULL DEFAULT NULL,
`path` 			varchar(2000) CHARACTER SET utf8mb4 NULL DEFAULT NULL,
`size` 			int(10) unsigned NULL DEFAULT NULL,
`status` 		enum('draft', 'awaiting', 'publish', 'block', 'filter', 'removed') NULL DEFAULT NULL,
`datecreated` 	timestamp NULL DEFAULT CURRENT_TIMESTAMP,
`datemodified` 	timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
CONSTRAINT `files_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



