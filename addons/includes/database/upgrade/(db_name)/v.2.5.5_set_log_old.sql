-- [database log]

ALTER TABLE `logs` ADD `expiredate` datetime NULL DEFAULT NULL;

UPDATE logs SET logs.expiredate = now() WHERE logs.notif = 1 AND logs.expiredate is null;