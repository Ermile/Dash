
ALTER TABLE `comments` ADD `via` enum('site', 'telegram', 'sms', 'contact', 'admincontact', 'app') NULL DEFAULT NULL;


UPDATE comments SET comments.via = 'contact' WHERE comments.type = 'ticket' AND comments.title IN ('Contact Us', 'تماس با ما');

UPDATE comments SET comments.via = 'telegram' WHERE comments.type = 'ticket' AND comments.title IN ('Ticket via telegram', 'تیکت از طریق تلگرام');

UPDATE comments SET comments.via = 'admincontact' WHERE comments.type = 'ticket' AND comments.title IN ('ارتباط با مدیریت');

UPDATE comments SET comments.via = 'site' WHERE comments.type = 'ticket' AND comments.title NOT IN ('Ticket via telegram', 'تیکت از طریق تلگرام', 'Contact Us', 'تماس با ما', 'ارتباط با مدیریت');






