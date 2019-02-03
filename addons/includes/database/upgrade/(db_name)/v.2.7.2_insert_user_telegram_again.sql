
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