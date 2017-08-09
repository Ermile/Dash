
--
-- Table structure for table `logitems`
--

CREATE TABLE IF NOT EXISTS `logitems` (
  `id` smallint(5) unsigned NOT NULL,
  `logitem_title` varchar(100) NOT NULL,
  `logitem_desc` varchar(999) DEFAULT NULL,
  `logitem_meta` mediumtext,
  `logitem_priority` enum('critical','high','medium','low') NOT NULL DEFAULT 'medium',
  `date_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;


--
-- Indexes for dumped tables
--

--
-- Indexes for table `logitems`
--
ALTER TABLE `logitems`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `logitems`
--
ALTER TABLE `logitems`
  MODIFY `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=18;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
