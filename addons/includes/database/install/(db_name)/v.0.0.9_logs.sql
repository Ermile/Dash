CREATE TABLE `logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `caller` varchar(200) DEFAULT NULL,
  `subdomain` varchar(100) DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `data` varchar(200) DEFAULT NULL,
  `datalink` varchar(100) DEFAULT NULL,
  `status` enum('enable','disable','expire','deliver') DEFAULT NULL,
  `datecreated` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `datemodified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `visitor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `meta` mediumtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `logs` ADD PRIMARY KEY (`id`);

ALTER TABLE `logs` MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
