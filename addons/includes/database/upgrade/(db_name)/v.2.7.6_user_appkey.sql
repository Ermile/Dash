ALTER TABLE `user_auth` CHANGE `type` `type` ENUM('guest','member','appkey') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `user_auth` ADD `parent` int(10) unsigned NULL DEFAULT NULL;
