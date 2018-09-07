CREATE TABLE `sendnotifications` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `status` enum('awaiting','sended','expire','cancel','cannotsend', 'turnoff') NULL DEFAULT 'awaiting',
  `way` enum('telegram','sms','email','call') NULL DEFAULT NULL,
  `to` varchar(500) CHARACTER SET utf8mb4 NULL,
  `text` text CHARACTER SET utf8mb4,
  `datecreated` timestamp DEFAULT CURRENT_TIMESTAMP,
  `datemodified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


