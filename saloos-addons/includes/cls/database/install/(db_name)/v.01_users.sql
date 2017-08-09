
--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL,
  `user_mobile` varchar(15) NOT NULL,
  `user_email` varchar(50) DEFAULT NULL,
  `user_pass` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `user_displayname` varchar(50) DEFAULT NULL,
  `user_meta` mediumtext,
  `user_status` enum('active','awaiting','deactive','removed','filter') DEFAULT 'awaiting',
  `user_parent` int(10) unsigned NULL DEFAULT NULL,
  `user_permission` smallint(5) unsigned DEFAULT NULL,
  `user_createdate` datetime NOT NULL,
  `date_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_mobile`, `user_email`, `user_pass`, `user_displayname`, `user_meta`, `user_status`, `user_permission`, `user_createdate`, `date_modified`) VALUES
(1, '989357269759', 'J.Evazzadeh@gmail.com', '$2y$07$9wj8/jDeQKyY0t0IcUf.xOEy98uf6BaSS7Tg28swrKUDxdKzUVfsy', 'Javad Evazzadeh', NULL, 'active', 1, '2015-01-01 00:00:00', NULL),
(2, '989356032043', 'itb.baravak@gmail.com', '$2y$07$ZRUphEsEn9bK8inKBfYt.efVoZDgBaoNfZz0uVRqRGvH9.che.Bqq', 'Hasan Salehi', NULL, 'active', 1, '2015-01-02 00:00:00', NULL),
(3, '989190499033', 'ahmadkarimi1991@gmail.com', '$2y$07$bLbhODUiPBFfbTU8V./m5OAYdkH2DP7uCQI2fVLubq7X/LdFQTeH.', 'Ahmad Karimi', NULL, 'active', 1, '2015-01-03 00:00:00', NULL),
(4, '989109610612', 'rm.biqarar@gmail.com', '$2y$07$k.Vi7QCpdym637.6rwbm2.u1tdMi4jyWFUg7YgNv.XnBFOP1.7W/y', 'Reza Mohiti', NULL, 'active', 1, '2015-01-04 00:00:00', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mobile_unique` (`user_mobile`) USING BTREE,
  ADD UNIQUE KEY `email_unique` (`user_email`) USING BTREE;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;