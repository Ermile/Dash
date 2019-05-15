-- [database log]

ALTER TABLE `logs` ADD INDEX `index_search_send` (`send`);
ALTER TABLE `logs` ADD INDEX `index_search_notif` (`notif`);
ALTER TABLE `logs` ADD INDEX `index_search_caller` (`caller`);
ALTER TABLE `logs` ADD INDEX `index_search_subdomain` (`subdomain`);
ALTER TABLE `logs` ADD INDEX `index_search_readdate` (`readdate`);
ALTER TABLE `logs` ADD INDEX `index_search_datecreated` (`datecreated`);


ALTER TABLE `sessions` ADD INDEX `index_search_code` (`code`);
ALTER TABLE `sessions` ADD INDEX `index_search_user_id` (`user_id`);
ALTER TABLE `sessions` ADD INDEX `index_search_status` (`status`);
ALTER TABLE `sessions` ADD INDEX `index_search_agent_id` (`agent_id`);

ALTER TABLE `agents` ADD INDEX `index_search_agentmd5` (`agentmd5`);
