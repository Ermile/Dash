ALTER TABLE `user_auth` CHANGE `status` `status` enum('enable','disable','expire', 'used') NULL DEFAULT NULL;
