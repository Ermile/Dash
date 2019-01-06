-- [database log]

ALTER TABLE `logs` DROP `telegramdate`;
ALTER TABLE `logs` DROP `smsdate`;
ALTER TABLE `logs` DROP `emaildate`;


-- ALTER TABLE `logs` ADD `ip` INT(10) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `logs` ADD `sms`  TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;
ALTER TABLE `logs` ADD `telegram`  TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;
ALTER TABLE `logs` ADD `email`  TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;


ALTER TABLE `logs` CHANGE `user_id` `to` INT(10) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `logs` CHANGE `user_idsender` `from` INT(10) UNSIGNED NULL DEFAULT NULL;

