CREATE TABLE invoices (
`id`					     int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
`date`					     datetime NOT NULL,
`user_id_seller`			 int(10) UNSIGNED NULL DEFAULT NULL,
`user_id`					 int(10) UNSIGNED NOT NULL,
`temp` 						 bit(1) NULL DEFAULT NULL,
`title`					     varchar(500) NOT NULL,
`total`					     bigint(20) NULL DEFAULT NULL,
`total_discount`			 int(10) NULL DEFAULT NULL,
`status`					 enum('enable','disable','expire') NOT NULL DEFAULT 'enable',
`date_pay`					 datetime NULL DEFAULT NULL,
`transaction_bank`	     	 varchar(255) NULL DEFAULT NULL,
`discount`					 int(10) NULL DEFAULT NULL,
`vat`					     int(10) NULL DEFAULT NULL,
`vat_pay`					 int(10) NULL DEFAULT NULL,
`final_total`				 bigint(20) NULL DEFAULT NULL,
`count_detail`			 	 smallint(5) UNSIGNED NULL DEFAULT NULL,
`createdate`				 datetime DEFAULT CURRENT_TIMESTAMP,
`date_modified`			 	 timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
`desc`					     text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
`meta`					     mediumtext  CHARACTER SET utf8mb4 NULL DEFAULT NULL,
PRIMARY KEY (`id`),
CONSTRAINT `inovoices_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
CONSTRAINT `inovoices_user_id_seller` FOREIGN KEY (`user_id_seller`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE invoice_details (
`id`					     int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
`invoice_id`			     int(10) UNSIGNED NOT NULL,
`title`					     varchar(500) NOT NULL,
`price`					     int(10) NULL DEFAULT NULL,
`count`					     smallint(5) NULL DEFAULT NULL,
`total`					     int(10) NULL DEFAULT NULL,
`discount`					 smallint(5) NULL DEFAULT NULL,
`status`					 enum('enable','disable','expire') NOT NULL DEFAULT 'enable',
`createdate`				 datetime DEFAULT CURRENT_TIMESTAMP,
`date_modified`			 	 timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
`desc`					     text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
`meta`					     mediumtext  CHARACTER SET utf8mb4 NULL DEFAULT NULL,
PRIMARY KEY (`id`),
CONSTRAINT `inovoices_id` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
