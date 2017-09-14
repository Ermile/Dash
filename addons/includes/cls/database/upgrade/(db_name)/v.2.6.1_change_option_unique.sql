ALTER TABLE `options` DROP INDEX `cat+key+value`;
ALTER TABLE `options` ADD UNIQUE KEY `unique_cat` (`cat`) USING BTREE;
ALTER TABLE `options` CHANGE `key` `key` varchar(100)  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;
