ALTER TABLE `users` ADD `verifymobile` bit(1) null default null AFTER `mobile`;
ALTER TABLE `users` ADD `verifyemail` bit(1) null default null AFTER `email`;

