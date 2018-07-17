ALTER TABLE `logs` CHANGE `caller` `caller` varchar(200) NULL DEFAULT NULL AFTER `id`;
ALTER TABLE `logs` CHANGE `data` `data` varchar(200) NULL DEFAULT NULL AFTER `user_id`;
ALTER TABLE `logs` ADD `datalink` varchar(100) NULL DEFAULT NULL AFTER `data`;
ALTER TABLE `logs` ADD `subdomain` varchar(100) NULL DEFAULT NULL AFTER `caller`;
ALTER TABLE `logs` DROP `before`;
ALTER TABLE `logs` DROP `after`;
ALTER TABLE `logs` DROP `desc`;
ALTER TABLE `logs` DROP `vars`;
ALTER TABLE `logs` DROP FOREIGN KEY `logs_logitems_id`;
ALTER TABLE `logs` DROP INDEX `logs_logitems_id`;
ALTER TABLE `logs` DROP `logitem_id`;
ALTER TABLE `logs` DROP `createdate`;
ALTER TABLE `logs` CHANGE `datecreated` `datecreated` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP AFTER `status`;
ALTER TABLE `logs` CHANGE `meta` `meta` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL AFTER `visitor_id`;

