ALTER TABLE `users` ADD `tgstatus` ENUM('active','deactive','spam','bot','block','unreachable','unknown','filter') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `users` CHANGE `tgstatus` `tgstatus` ENUM('active','deactive','spam','bot','block','unreachable','unknown','filter') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;