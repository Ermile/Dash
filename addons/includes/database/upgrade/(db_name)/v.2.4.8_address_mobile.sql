ALTER TABLE `address` ADD `mobile` varchar(20) DEFAULT NULL AFTER `phone`;
ALTER TABLE `address` ADD `name` varchar(200) DEFAULT NULL AFTER `firstname`;
ALTER TABLE `address` DROP `firstname`;
ALTER TABLE `address` DROP `lastname`;

