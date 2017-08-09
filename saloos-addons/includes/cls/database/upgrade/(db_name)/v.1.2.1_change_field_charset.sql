-- users
ALTER TABLE `users` CHANGE `user_email` `user_email` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL;
ALTER TABLE `users` CHANGE `user_displayname` `user_displayname` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL;
ALTER TABLE `users` CHANGE `user_meta` `user_meta` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL;
-- terms
ALTER TABLE `terms` CHANGE `term_url` `term_url` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL;
ALTER TABLE `terms` CHANGE `term_type` `term_type` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT 'tag';
ALTER TABLE `terms` CHANGE `term_caller` `term_caller` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL;
ALTER TABLE `terms` CHANGE `term_title` `term_title` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL;
ALTER TABLE `terms` CHANGE `term_slug` `term_slug` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL;
ALTER TABLE `terms` CHANGE `term_desc` `term_desc` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL;
ALTER TABLE `terms` CHANGE `term_meta` `term_meta` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL;
-- options
ALTER TABLE `options` CHANGE `option_cat` `option_cat` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL;
ALTER TABLE `options` CHANGE `option_key` `option_key` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL;
ALTER TABLE `options` CHANGE `option_value` `option_value` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL;
ALTER TABLE `options` CHANGE `option_meta` `option_meta` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL;
-- logitems
ALTER TABLE `logitems` CHANGE `logitem_type` `logitem_type` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL;
ALTER TABLE `logitems` CHANGE `logitem_caller` `logitem_caller` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL;
ALTER TABLE `logitems` CHANGE `logitem_title` `logitem_title` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL;
ALTER TABLE `logitems` CHANGE `logitem_desc` `logitem_desc` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL;
ALTER TABLE `logitems` CHANGE `logitem_meta` `logitem_meta` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL;
-- logs
ALTER TABLE `logs` CHANGE `log_data` `log_data` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL;
ALTER TABLE `logs` CHANGE `log_meta` `log_meta` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL;
-- notification
ALTER TABLE `notifications` CHANGE `notification_title` `notification_title` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL;
ALTER TABLE `notifications` CHANGE `notification_content` `notification_content` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL;
ALTER TABLE `notifications` CHANGE `notification_meta` `notification_meta` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL;
ALTER TABLE `notifications` CHANGE `notification_url` `notification_url` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL;
-- comments
ALTER TABLE `comments` CHANGE `comment_author` `comment_author` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL;
ALTER TABLE `comments` CHANGE `comment_email` `comment_email` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL;
ALTER TABLE `comments` CHANGE `comment_url` `comment_url` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL;
ALTER TABLE `comments` CHANGE `comment_content` `comment_content` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL;
ALTER TABLE `comments` CHANGE `comment_meta` `comment_meta` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL;
-- posts
ALTER TABLE `posts` CHANGE `post_title` `post_title` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL;
ALTER TABLE `posts` CHANGE `post_slug` `post_slug` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL;
ALTER TABLE `posts` CHANGE `post_content` `post_content` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL;
ALTER TABLE `posts` CHANGE `post_meta` `post_meta` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL;
ALTER TABLE `posts` CHANGE `post_type` `post_type` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT 'post';