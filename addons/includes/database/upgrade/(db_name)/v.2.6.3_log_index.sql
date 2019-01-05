-- [database log]

ALTER TABLE `logs` ADD INDEX `log_expiredate` (`expiredate`);
ALTER TABLE `logs` ADD INDEX `log_caller` (`caller`);
ALTER TABLE `logs` ADD INDEX `log_code` (`code`);


