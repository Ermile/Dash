ALTER TABLE `transactions` ADD `token` varchar(32) DEFAULT NULL;
ALTER TABLE `transactions` ADD `banktoken` varchar(100) DEFAULT NULL;


ALTER TABLE `transactions` ADD INDEX `transactions_index_token` (`token`);
ALTER TABLE `transactions` ADD INDEX `transactions_index_banktoken` (`banktoken`);
ALTER TABLE `transactions` ADD INDEX `transactions_index_payment` (`payment`);
ALTER TABLE `transactions` ADD INDEX `transactions_index_dateverify` (`dateverify`);
ALTER TABLE `transactions` ADD INDEX `transactions_index_verify` (`verify`);
ALTER TABLE `transactions` ADD INDEX `transactions_index_plus` (`plus`);
ALTER TABLE `transactions` ADD INDEX `transactions_index_minus` (`minus`);
