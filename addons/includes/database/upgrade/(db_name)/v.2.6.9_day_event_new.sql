-- [database log]

ALTER TABLE `dayevent` ADD `help` int(10) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `dayevent` ADD `attachment` int(10) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `dayevent` ADD `tag` int(10) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `dayevent` ADD `cat` int(10) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `dayevent` ADD `support_tag` int(10) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `dayevent` ADD `help_tag` int(10) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `dayevent` ADD `user_mobile` int(10) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `dayevent` ADD `user_email` int(10) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `dayevent` ADD `user_chatid` int(10) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `dayevent` ADD `user_username` int(10) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `dayevent` ADD `user_android` int(10) UNSIGNED NULL DEFAULT NULL; -- android_uniquecode
ALTER TABLE `dayevent` ADD `user_awaiting` int(10) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `dayevent` ADD `user_removed` int(10) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `dayevent` ADD `user_filter` int(10) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `dayevent` ADD `user_unreachabl` int(10) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `dayevent` ADD `user_permission` int(10) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `dayevent` ADD `ticket_message` int(10) UNSIGNED NULL DEFAULT NULL;