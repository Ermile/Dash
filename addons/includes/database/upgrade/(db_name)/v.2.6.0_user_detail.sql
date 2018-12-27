
ALTER TABLE `users` ADD `father` varchar(100) DEFAULT NULL;
ALTER TABLE `users` ADD `nationalcode` varchar(50) DEFAULT NULL;
ALTER TABLE `users` ADD `nationality` varchar(5) DEFAULT NULL;
ALTER TABLE `users` ADD `pasportcode` varchar(50) DEFAULT NULL;
ALTER TABLE `users` ADD `pasportdate` varchar(20) DEFAULT NULL;
ALTER TABLE `users` ADD `marital` enum('single','married') DEFAULT NULL;
ALTER TABLE `users` ADD `foreign` bit(1) DEFAULT NULL;
ALTER TABLE `users` ADD `phone` varchar(1000) DEFAULT NULL;
ALTER TABLE `users` ADD `detail` text CHARACTER SET utf8mb4;





