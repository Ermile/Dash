-- [database log]

CREATE TABLE IF NOT EXISTS `telegrams` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`chatid` bigint(20) DEFAULT NULL,
`user_id` int(10) unsigned DEFAULT NULL,
`step` text CHARACTER SET utf8mb4,
`hook` mediumtext CHARACTER SET utf8mb4,
`hooktext` text CHARACTER SET utf8mb4,
`hookdate` datetime DEFAULT NULL,
`hookmessageid` text CHARACTER SET utf8mb4,
`send` mediumtext CHARACTER SET utf8mb4,
`senddate` datetime DEFAULT NULL,
`sendtext` text CHARACTER SET utf8mb4,
`sendmesageid` text CHARACTER SET utf8mb4,
`sendmethod` text CHARACTER SET utf8mb4,
`sendkeyboard` text CHARACTER SET utf8mb4,
`response` mediumtext CHARACTER SET utf8mb4,
`responsedate` datetime DEFAULT NULL,
`status` enum('enable','disable','ok','failed','other') DEFAULT NULL,
`url` text CHARACTER SET utf8mb4,
`meta` mediumtext CHARACTER SET utf8mb4,
`send2` mediumtext CHARACTER SET utf8mb4,
`response2` mediumtext CHARACTER SET utf8mb4,
`send3` mediumtext CHARACTER SET utf8mb4,
`response3` mediumtext CHARACTER SET utf8mb4,
PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

