-- [database log]

CREATE TABLE `sessions` (
`id` bigint(20) UNSIGNED NOT NULL,
`code` varchar(64) NOT NULL,
`user_id` int(10) UNSIGNED NOT NULL,
`status` enum('active','terminate','expire','disable','changed','logout') NOT NULL DEFAULT 'active',
`agent_id` int(10) UNSIGNED DEFAULT NULL,
`ip` int(10) UNSIGNED DEFAULT NULL,
`count` int(10) UNSIGNED DEFAULT '1',
`datecreated` timestamp DEFAULT CURRENT_TIMESTAMP,
`datemodified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
`last_seen` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique` (`code`) USING BTREE,
  ADD KEY `sessions_user_id` (`user_id`);

ALTER TABLE `sessions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_agent_id` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`) ON UPDATE CASCADE;

