-- [database log]

ALTER TABLE visitors ADD `country` CHAR(2) NULL DEFAULT NULL;
ALTER TABLE visitors ADD `method` varchar(50) NULL DEFAULT NULL;