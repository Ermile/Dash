ALTER TABLE `users` ADD `user_pin` smallint(4) unsigned NULL DEFAULT NULL;
ALTER TABLE `users` ADD `user_ref` int(10) unsigned NULL DEFAULT NULL;
ALTER TABLE `users` ADD `user_creator` int(10) unsigned NULL DEFAULT NULL;
ALTER TABLE `users` CHANGE `user_pass` `user_pass` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL;
