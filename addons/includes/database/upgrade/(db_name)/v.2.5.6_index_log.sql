-- [database log]

ALTER TABLE `logs` ADD INDEX `log_status_index` (`status`);
ALTER TABLE `logs` ADD INDEX `log_to_index` (`to`);

