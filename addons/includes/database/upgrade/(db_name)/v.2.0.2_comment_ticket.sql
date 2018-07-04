ALTER TABLE `comments` ADD `title` varchar(500) NULL DEFAULT NULL;
ALTER TABLE `comments` CHANGE `status` `status` ENUM('approved','awaiting','unapproved','spam','deleted','filter','close', 'answered') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'awaiting';
ALTER TABLE `comments` ADD `file` varchar(2000) NULL DEFAULT NULL;