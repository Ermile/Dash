-- [database log]

ALTER TABLE `logs` CHANGE `status` `status` ENUM('enable','disable','expire','deliver','awaiting','deleted','cancel','block', 'notif', 'notifread', 'notifexpire') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
