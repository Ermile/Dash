
--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` bigint(20) unsigned NOT NULL,
  `post_id` bigint(20) unsigned DEFAULT NULL,
  `comment_author` varchar(50) DEFAULT NULL,
  `comment_email` varchar(100) DEFAULT NULL,
  `comment_url` varchar(100) DEFAULT NULL,
  `comment_content` mediumtext NOT NULL,
  `comment_meta` mediumtext,
  `comment_status` enum('approved','unapproved','spam','deleted') NOT NULL DEFAULT 'unapproved',
  `comment_parent` smallint(5) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `visitor_id` bigint(20) unsigned DEFAULT NULL,
  `date_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comments_posts_id` (`post_id`) USING BTREE,
  ADD KEY `comments_users_id` (`user_id`) USING BTREE,
  ADD KEY `comments_visitors_id` (`visitor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_posts_id` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comments_users_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
