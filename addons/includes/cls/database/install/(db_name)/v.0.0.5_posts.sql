CREATE TABLE `posts` (
`id` bigint(20) UNSIGNED NOT NULL,
`post_language` char(2) DEFAULT NULL,
`post_title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
`post_slug` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
`post_url` varchar(255) NOT NULL,
`post_content` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
`post_meta` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
`post_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT 'post',
`post_comment` enum('open','closed') DEFAULT NULL,
`post_count` smallint(5) UNSIGNED DEFAULT NULL,
`post_order` int(10) UNSIGNED DEFAULT NULL,
`post_status` enum('publish','draft','schedule','deleted','expire') NOT NULL DEFAULT 'draft',
`post_parent` bigint(20) UNSIGNED DEFAULT NULL,
`user_id` int(10) UNSIGNED NOT NULL,
`post_publishdate` datetime DEFAULT NULL,
`date_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `posts`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `url_unique` (`post_url`,`post_language`) USING BTREE,
ADD KEY `posts_users_id` (`user_id`) USING BTREE;


ALTER TABLE `posts`
MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;


ALTER TABLE `posts`
ADD CONSTRAINT `posts_users_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;
