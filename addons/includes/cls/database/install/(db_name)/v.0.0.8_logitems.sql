
CREATE TABLE `logitems` (
`id` smallint(5) UNSIGNED NOT NULL,
`logitem_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
`logitem_caller` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
`logitem_title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
`logitem_desc` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
`logitem_meta` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
`count` int(10) UNSIGNED NOT NULL DEFAULT '0',
`logitem_priority` enum('critical','high','medium','low') NOT NULL DEFAULT 'medium',
`date_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `logitems` ADD PRIMARY KEY (`id`);
ALTER TABLE `logitems` MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;