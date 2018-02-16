CREATE TABLE `posts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `language` char(2) DEFAULT NULL,
  `title` varchar(200) CHARACTER SET utf8mb4 NOT NULL,
  `slug` varchar(200) CHARACTER SET utf8mb4 NOT NULL,
  `url` varchar(2000) NOT NULL,
   `excerpt` varchar(500)  NULL,
   `subtitle` varchar(500)  NULL,
  `content` mediumtext CHARACTER SET utf8mb4,
  `meta` mediumtext CHARACTER SET utf8mb4,
  `type` varchar(100) CHARACTER SET utf8mb4 NOT NULL DEFAULT 'post',
  `comment` enum('open','closed') DEFAULT NULL,
  `count` smallint(5) UNSIGNED DEFAULT NULL,
  `order` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('publish','draft','schedule','deleted','expire') NOT NULL DEFAULT 'draft',
  `parent` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `publishdate` datetime DEFAULT NULL,
  `datemodified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `datecreated` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `posts_users_id` (`user_id`) USING BTREE;


ALTER TABLE `posts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `posts`
  ADD CONSTRAINT `posts_users_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

