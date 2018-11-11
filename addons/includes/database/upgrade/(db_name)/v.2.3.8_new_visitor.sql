-- [database log]

DROP TABLE IF EXISTS `visitors`;
DROP TABLE IF EXISTS `urls`;
DROP TABLE IF EXISTS `services`;
DROP TABLE IF EXISTS `logitems`;


CREATE TABLE `agents` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `agent` text NOT NULL,
  `group` varchar(50) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `version` varchar(50) DEFAULT NULL,
  `os` varchar(50) DEFAULT NULL,
  `osnum` varchar(50) DEFAULT NULL,
  `robot` bit(1) DEFAULT NULL,
  `meta` text,
  `datecreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `datemodified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `agentmd5` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `urls` (
`id`		INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
`urlmd5`	VARCHAR(32) CHARACTER SET utf8mb4 DEFAULT NULL,
`domain`	VARCHAR(500) CHARACTER SET utf8mb4 DEFAULT NULL,
`subdomain`	VARCHAR(200) CHARACTER SET utf8mb4 DEFAULT NULL,
`path`		text CHARACTER SET utf8mb4 NULL,
`query`		text CHARACTER SET utf8mb4 NULL,
`pwd`		text CHARACTER SET utf8mb4 NULL,
`datecreated`	timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
INDEX `urlmd5_index` (`urlmd5`),
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `visitors` (
`id`			bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
`statuscode`	int(5) DEFAULT NULL,
`visitor_ip`	int(10) UNSIGNED DEFAULT NULL,
`session_id`	VARCHAR(100) NULL,
`url_id`		int(10) UNSIGNED NOT NULL,
`url_idreferer`	int(10) UNSIGNED DEFAULT NULL,
`agent_id`		int(10) UNSIGNED DEFAULT NULL,
`user_id`		int(10) UNSIGNED DEFAULT NULL,
`date`			timestamp NOT NULL,
`avgtime`		int(10) UNSIGNED DEFAULT NULL,
PRIMARY KEY (`id`),
CONSTRAINT `visitors_agents` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`) ON UPDATE CASCADE,
CONSTRAINT `visitors_urls` FOREIGN KEY (`url_id`) REFERENCES `urls` (`id`) ON UPDATE CASCADE,
CONSTRAINT `visitors_urls_referer` FOREIGN KEY (`url_idreferer`) REFERENCES `urls` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


