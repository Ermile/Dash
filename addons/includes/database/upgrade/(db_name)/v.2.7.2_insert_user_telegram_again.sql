
INSERT INTO user_telegram
(
	`user_id`,
	`chatid`,
	`firstname`,
	`lastname`,
	`username`,
	`language`,
	`status`,
	`lastupdate`
)
SELECT
	users.id,
	users.chatid,
	users.firstname,
	users.lastname,
	users.tgusername,
	users.language,
	users.tgstatus,
	users.tg_lastupdate
FROM
	users
WHERE users.chatid IS NOT NULL AND users.chatid NOT IN (SELECT uC.chatid FROM user_telegram AS `uC` );



ALTER TABLE `users` DROP `chatid`;
ALTER TABLE `users` DROP `tgusername`;
ALTER TABLE `users` DROP `tgstatus`;
ALTER TABLE `users` DROP `tg_lastupdate`;



ALTER TABLE `users` DROP INDEX `index_search_android_uniquecode`;
ALTER TABLE `users` DROP `android_version`;
ALTER TABLE `users` DROP `android_serial`;
ALTER TABLE `users` DROP `android_model`;
ALTER TABLE `users` DROP `android_manufacturer`;
ALTER TABLE `users` DROP `android_lastupdate`;
ALTER TABLE `users` DROP `android_uniquecode`;
ALTER TABLE `users` DROP `android_meta`;