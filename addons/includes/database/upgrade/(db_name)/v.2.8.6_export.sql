-- [database log]

CREATE TABLE IF NOT EXISTS `export` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(10) unsigned DEFAULT NULL,
`customid` varchar(100) CHARACTER SET utf8mb4 NULL DEFAULT NULL,
`caller` varchar(100) CHARACTER SET utf8mb4 NULL DEFAULT NULL,
`title` varchar(200) CHARACTER SET utf8mb4 NULL DEFAULT NULL,
`desc` text CHARACTER SET utf8mb4,
`fn` varchar(100) CHARACTER SET utf8mb4 NULL DEFAULT NULL,
`status` enum('request', 'awaiting', 'runing', 'done', 'fail', 'downloaded', 'removed') NULL DEFAULT NULL,
`file` text CHARACTER SET utf8mb4,
`filesize` int(10) unsigned NULL DEFAULT NULL,
`datecreated` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
`datemodified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

