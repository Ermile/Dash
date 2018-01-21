CREATE TABLE `terms` (
  `id` int(10) UNSIGNED NOT NULL,
  `language` char(2) DEFAULT NULL,
  `type` enum('cat','tag','code','other','term') DEFAULT NULL,
  `title` varchar(200) CHARACTER SET utf8mb4 NOT NULL,
  `excerpt` varchar(300) CHARACTER SET utf8mb4  NULL,
  `slug` varchar(200) CHARACTER SET utf8mb4 NOT NULL,
  `url` varchar(2000) CHARACTER SET utf8mb4 DEFAULT NULL,
  `desc` mediumtext CHARACTER SET utf8mb4,
  `meta` mediumtext CHARACTER SET utf8mb4,
  `parent` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('enable','disable','expired','awaiting','filtered','blocked','spam','violence','pornography','other') NOT NULL DEFAULT 'awaiting',
  `datecreated` timestamp DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `terms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `terms_users_id` (`user_id`);


ALTER TABLE `terms`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `terms`
  ADD CONSTRAINT `terms_users_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

