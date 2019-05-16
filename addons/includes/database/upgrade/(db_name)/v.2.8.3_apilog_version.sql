-- [database log]
ALTER TABLE `apilog` ADD `version` varchar(100) NULL DEFAULT NULL;
ALTER TABLE `apilog` ADD `responselen` int(10) unsigned NULL DEFAULT NULL;



ALTER TABLE `apilog` ADD INDEX `index_search_version` (`version`);
ALTER TABLE `apilog` ADD INDEX `index_search_token` (`token`);
ALTER TABLE `apilog` ADD INDEX `index_search_apikey` (`apikey`);
ALTER TABLE `apilog` ADD INDEX `index_search_appkey` (`appkey`);
ALTER TABLE `apilog` ADD INDEX `index_search_zoneid` (`zoneid`);

ALTER TABLE `apilog` ADD INDEX `index_search_method` (`method`);
ALTER TABLE `apilog` ADD INDEX `index_search_headerlen` (`headerlen`);
ALTER TABLE `apilog` ADD INDEX `index_search_bodylen` (`bodylen`);
ALTER TABLE `apilog` ADD INDEX `index_search_pagestatus` (`pagestatus`);
ALTER TABLE `apilog` ADD INDEX `index_search_resultstatus` (`resultstatus`);
ALTER TABLE `apilog` ADD INDEX `index_search_responselen` (`responselen`);



