-- [database log]

CREATE TABLE IF NOT EXISTS `apilog` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(10) unsigned DEFAULT NULL,
`token` varchar(100) CHARACTER SET utf8mb4 NULL DEFAULT NULL,
`apikey` varchar(100) CHARACTER SET utf8mb4 NULL DEFAULT NULL,
`appkey` varchar(100) CHARACTER SET utf8mb4 NULL DEFAULT NULL,
`zoneid` varchar(100) CHARACTER SET utf8mb4 NULL DEFAULT NULL,
`url` varchar(2000) CHARACTER SET utf8mb4,
`method` varchar(200) CHARACTER SET utf8mb4 NULL DEFAULT NULL,
`header` mediumtext CHARACTER SET utf8mb4,
`headerlen` int(10) unsigned DEFAULT NULL,
`body` mediumtext CHARACTER SET utf8mb4,
`bodylen` int(10) unsigned DEFAULT NULL,
`datesend` timestamp NULL DEFAULT NULL,
`pagestatus` varchar(100) CHARACTER SET utf8mb4 NULL DEFAULT NULL,
`resultstatus` varchar(100) CHARACTER SET utf8mb4 NULL DEFAULT NULL,
`responseheader` mediumtext CHARACTER SET utf8mb4,
`responsebody` mediumtext CHARACTER SET utf8mb4,
`dateresponse` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

