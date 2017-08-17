ALTER TABLE `transactions` ADD `invoice_id`  int(10) unsigned NULL DEFAULT NULL;
-- ALTER TABLE `transactions` ADD `date_temp`  datetime NULL DEFAULT NULL;
-- UPDATE transactions SET  transactions.date_temp = CONCAT(transactions.date , ' ', transactions.time);
-- ALTER TABLE `transactions` DROP  `date`;
-- ALTER TABLE `transactions` DROP  `time`;
-- ALTER TABLE `transactions` CHANGE `date_temp` `date` datetime NULL DEFAULT NULL;
