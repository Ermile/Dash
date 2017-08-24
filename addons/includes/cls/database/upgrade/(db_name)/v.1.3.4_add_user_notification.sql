ALTER TABLE `users` ADD `user_notification` TEXT NULL DEFAULT NULL;

ALTER TABLE `notifications` ADD `telegram` bit(1) NULL DEFAULT NULL;
ALTER TABLE `notifications` ADD `telegramdate` datetime NULL DEFAULT NULL;
ALTER TABLE `notifications` ADD `sms` bit(1) NULL DEFAULT NULL;
ALTER TABLE `notifications` ADD `smsdate` datetime NULL DEFAULT NULL;
ALTER TABLE `notifications` ADD `smsdeliverdate` datetime NULL DEFAULT NULL;
ALTER TABLE `notifications` ADD `email` bit(1) NULL DEFAULT NULL;
ALTER TABLE `notifications` ADD `emaildate` datetime NULL DEFAULT NULL;

ALTER TABLE `notifications` CHANGE `status` `status` enum('awaiting','enable','disable','expire', 'deleted', 'cancel', 'block') NULL DEFAULT NULL;

ALTER TABLE `notifications` DROP `auto`;
ALTER TABLE `notifications` DROP `gateway`;
ALTER TABLE `notifications` DROP `senddate`;
ALTER TABLE `notifications` DROP `deliverdate`;

ALTER TABLE `notifications` ADD `related_foreign` varchar(50) NULL DEFAULT NULL;
ALTER TABLE `notifications` ADD `related_id` bigint(20) unsigned NULL DEFAULT NULL;
ALTER TABLE `notifications` ADD `needanswer` bit(1) NULL DEFAULT NULL;
ALTER TABLE `notifications` ADD `answer` smallint(3) NULL DEFAULT NULL;