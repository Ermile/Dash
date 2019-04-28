ALTER TABLE `termusages` ADD INDEX `termusages_term_id_search_index` (`term_id`);
ALTER TABLE `termusages` ADD INDEX `termusages_related_id_search_index` (`related_id`);
ALTER TABLE `termusages` ADD INDEX `termusages_related_search_index` (`related`);
ALTER TABLE `termusages` ADD INDEX `termusages_type_search_index` (`type`);
ALTER TABLE `terms` ADD INDEX `terms_type_search_index` (`type`);

