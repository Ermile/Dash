ALTER TABLE `comments` ADD `comment_type` ENUM('comment', 'rate') NULL DEFAULT NULL AFTER `user_id`;
ALTER TABLE `comments` ADD `comment_plus` int(10) unsigned NULL AFTER `user_id`;
ALTER TABLE `comments` ADD `comment_minus` int(10) unsigned NULL AFTER `user_id`;