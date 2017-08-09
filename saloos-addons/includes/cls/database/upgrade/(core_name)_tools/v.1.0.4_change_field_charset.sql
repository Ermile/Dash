UPDATE `passwords` SET `createdate` = NULL WHERE 1;
-- saloos tools password
ALTER TABLE `passwords` CHANGE `password` `password` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL;
ALTER TABLE `passwords` CHANGE `substatus` `substatus` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL;
-- saloos tools words
ALTER TABLE `words` CHANGE `word` `word` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL;
ALTER TABLE `words` CHANGE `slug` `slug` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL;