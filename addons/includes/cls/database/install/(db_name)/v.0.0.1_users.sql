CREATE TABLE `users` (
`id` int(10) UNSIGNED NOT NULL,
`user_mobile` varchar(15) DEFAULT NULL,
`user_email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
`user_pass` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
`user_displayname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
`user_meta` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
`user_status` enum('active','awaiting','deactive','removed','filter','unreachable') DEFAULT 'awaiting',
`user_parent` int(10) UNSIGNED DEFAULT NULL,
`user_permission` varchar(1000) DEFAULT NULL,
`user_createdate` datetime NOT NULL,
`date_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
`user_username` varchar(100) CHARACTER SET utf8mb4 DEFAULT NULL,
`user_group` varchar(100) CHARACTER SET utf8mb4 DEFAULT NULL,
`user_file_id` int(20) UNSIGNED DEFAULT NULL,
`user_chat_id` int(20) UNSIGNED DEFAULT NULL,
`user_pin` smallint(4) UNSIGNED DEFAULT NULL,
`user_ref` int(10) UNSIGNED DEFAULT NULL,
`user_creator` int(10) UNSIGNED DEFAULT NULL,
`user_two_step` bit(1) DEFAULT NULL,
`user_google_mail` varchar(100) DEFAULT NULL,
`user_facebook_mail` varchar(100) DEFAULT NULL,
`user_twitter_mail` varchar(100) DEFAULT NULL,
`user_dont_will_set_mobile` varchar(50) DEFAULT NULL,
`user_file_url` varchar(2000) DEFAULT NULL,
`user_notification` text,
`user_setup` bit(1) DEFAULT NULL,
`user_name` varchar(100) DEFAULT NULL,
`user_family` varchar(100) DEFAULT NULL,
`user_father` varchar(100) DEFAULT NULL,
`user_birthday` datetime DEFAULT NULL,
`user_code` varchar(100) DEFAULT NULL,
`user_nationalcode` varchar(100) DEFAULT NULL,
`user_from` varchar(100) DEFAULT NULL,
`user_nationality` varchar(100) DEFAULT NULL,
`user_brithplace` varchar(100) DEFAULT NULL,
`user_region` varchar(100) DEFAULT NULL,
`user_pasportcode` varchar(100) DEFAULT NULL,
`user_marital` enum('single','marride') DEFAULT NULL,
`user_gender` enum('male','female') DEFAULT NULL,
`user_childcount` smallint(2) DEFAULT NULL,
`user_education` varchar(100) DEFAULT NULL,
`user_insurancetype` varchar(100) DEFAULT NULL,
`user_insurancecode` varchar(100) DEFAULT NULL,
`user_dependantscount` smallint(4) DEFAULT NULL,
`user_postion` varchar(100) DEFAULT NULL,
`unit_id` smallint(5) DEFAULT NULL,
`user_language` char(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `users` ADD PRIMARY KEY (`id`);

ALTER TABLE `users` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;


INSERT INTO `users` (`id`, `user_mobile`, `user_email`, `user_pass`, `user_displayname`, `user_meta`, `user_status`, `user_permission`, `user_createdate`, `date_modified`) VALUES
(1, '989357269759', 'J.Evazzadeh@gmail.com', '$2y$07$9wj8/jDeQKyY0t0IcUf.xOEy98uf6BaSS7Tg28swrKUDxdKzUVfsy', 'Javad Evazzadeh', NULL, 'active', 'admin', '2015-01-01 00:00:00', NULL),
(2, '989356032043', 'itb.baravak@gmail.com', '$2y$07$ZRUphEsEn9bK8inKBfYt.efVoZDgBaoNfZz0uVRqRGvH9.che.Bqq', 'Hasan Salehi', NULL, 'active', NULL, '2015-01-02 00:00:00', NULL),
(3, '989190499033', 'ahmadkarimi1991@gmail.com', '$2y$07$bLbhODUiPBFfbTU8V./m5OAYdkH2DP7uCQI2fVLubq7X/LdFQTeH.', 'Ahmad Karimi', NULL, 'active', NULL, '2015-01-03 00:00:00', NULL),
(4, '989109610612', 'rm.biqarar@gmail.com', '$2y$07$k.Vi7QCpdym637.6rwbm2.u1tdMi4jyWFUg7YgNv.XnBFOP1.7W/y', 'Reza Mohiti', NULL, 'active', NULL, '2015-01-04 00:00:00', NULL);

