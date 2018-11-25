-- [database log]

DROP TABLE IF EXISTS `logs`;


CREATE TABLE `logs` (
`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
`caller` varchar(200) DEFAULT NULL,
`subdomain` varchar(100) DEFAULT NULL,
`user_id` int(10) UNSIGNED DEFAULT NULL,
`notif` bit(1) DEFAULT NULL,
`user_idsender` int(10) UNSIGNED DEFAULT NULL,
`ip` INT(10) UNSIGNED NULL DEFAULT NULL,
`readdate` timestamp NULL DEFAULT NULL,
`telegramdate` timestamp NULL DEFAULT NULL,
`smsdate` timestamp NULL DEFAULT NULL,
`emaildate` timestamp NULL DEFAULT NULL,
`data` text CHARACTER SET utf8mb4 NULL,
`status` enum('enable','disable','expire','deliver', 'awaiting','deleted','cancel','block') DEFAULT NULL,
`datecreated` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
`datemodified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
`visitor_id` bigint(20) UNSIGNED DEFAULT NULL,
`meta` mediumtext CHARACTER SET utf8mb4,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


