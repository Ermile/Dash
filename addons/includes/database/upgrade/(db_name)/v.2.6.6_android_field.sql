
ALTER TABLE `users` ADD `android_version` varchar(200) DEFAULT NULL;
ALTER TABLE `users` ADD `android_serial` varchar(200) DEFAULT NULL;
ALTER TABLE `users` ADD `android_model` varchar(200) DEFAULT NULL;
ALTER TABLE `users` ADD `android_manufacturer` varchar(200) DEFAULT NULL;
ALTER TABLE `users` ADD `android_lastupdate` datetime DEFAULT NULL;
ALTER TABLE `users` ADD `android_uniquecode` char(32) DEFAULT NULL;
ALTER TABLE `users` ADD `android_meta` text CHARACTER SET utf8mb4;

ALTER TABLE `users` ADD INDEX `index_search_android_uniquecode` (`android_uniquecode`);

ALTER TABLE `users` ADD `tg_lastupdate` datetime DEFAULT NULL AFTER `chatid`;

