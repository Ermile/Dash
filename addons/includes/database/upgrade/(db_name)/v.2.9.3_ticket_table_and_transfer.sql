 CREATE TABLE `tickets` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) UNSIGNED DEFAULT NULL,
  `author` varchar(100) CHARACTER SET utf8mb4 DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 DEFAULT NULL,
  `url` varchar(100) CHARACTER SET utf8mb4 DEFAULT NULL,
  `content` mediumtext CHARACTER SET utf8mb4 NOT NULL,
  `meta` mediumtext CHARACTER SET utf8mb4,
  `status` enum('approved','awaiting','unapproved','spam','deleted','filter','close','answered') NOT NULL DEFAULT 'awaiting',
  `parent` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `minus` int(10) UNSIGNED DEFAULT NULL,
  `plus` int(10) UNSIGNED DEFAULT NULL,
  `star` smallint(5) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `visitor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `datemodified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `datecreated` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` int(10) UNSIGNED DEFAULT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `title` varchar(500) DEFAULT NULL,
  `file` varchar(2000) DEFAULT NULL,
  `answertime` int(10) UNSIGNED DEFAULT NULL,
  `subdomain` varchar(200) DEFAULT NULL,
  `solved` bit(1) DEFAULT NULL,
  `via` enum('site','telegram','sms','contact','admincontact','app') DEFAULT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;





ALTER TABLE `tickets`
  ADD KEY `tickets_posts_id` (`post_id`) USING BTREE,
  ADD KEY `tickets_users_id` (`user_id`) USING BTREE,
  ADD KEY `tickets_visitors_id` (`visitor_id`),
  ADD KEY `index_search_subdomain` (`subdomain`),
  ADD KEY `index_search_star` (`star`),
  ADD KEY `index_search_minus` (`minus`),
  ADD KEY `index_search_plus` (`plus`),
  ADD KEY `index_search_post_id` (`post_id`),
  ADD KEY `index_search_user_id` (`user_id`),
  ADD KEY `index_search_status` (`status`),
  ADD KEY `index_search_type` (`type`);


ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_posts_id` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tickets_users_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `terms` CHANGE `type` `type` enum('cat','tag','code','other','term', 'support_tag', 'mag', 'mag_tag','help', 'help_tag') DEFAULT NULL;
ALTER TABLE `termusages` CHANGE `type` `type` enum('cat','tag','term','code','other','support_tag', 'mag', 'mag_tag','help', 'help_tag','barcode1','barcode2','barcode3','qrcode1','qrcode2','qrcode3','rfid1','rfid2','rfid3','fingerprint1','fingerprint2','fingerprint3','fingerprint4','fingerprint5','fingerprint6','fingerprint7','fingerprint8','fingerprint9','fingerprint10') DEFAULT NULL;
ALTER TABLE `termusages` CHANGE `related` `related` ENUM('posts','products','attachments','files','comments','users', 'tickets') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

UPDATE termusages SET termusages.related = 'tickets' WHERE termusages.related = 'comments';


INSERT INTO tickets SELECT * FROM comments WHERE comments.type IN('ticket', 'ticket_note');


ALTER TABLE `tickets` ADD `see` bit(1) NULL DEFAULT NULL;