-- [database log]

ALTER TABLE `logs` ADD `code` varchar(200) DEFAULT NULL AFTER `subdomain`;
ALTER TABLE `logs` ADD `send` bit(1) DEFAULT NULL AFTER `code`;
