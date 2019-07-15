INSERT INTO files
(
	`user_id`,
	`md5`,
	`filename`,
	`title`,
	`desc`,
	`useage`,
	`type`,
	`mime`,
	`ext`,
	`folder`,
	`path`,
	`url`,
	`size`,
	`status`,
	`datecreated`
)
SELECT
	posts.user_id,
	posts.slug,
	posts.title,
	null,
	null,
	null,
	TRIM(BOTH '"' FROM JSON_EXTRACT(posts.meta, '$.type')),
	TRIM(BOTH '"' FROM JSON_EXTRACT(posts.meta, '$.mime')),
	TRIM(BOTH '"' FROM JSON_EXTRACT(posts.meta, '$.ext')),
	null,
	TRIM(BOTH '"' FROM JSON_EXTRACT(posts.meta, '$.url')),
	TRIM(BOTH '"' FROM JSON_EXTRACT(posts.meta, '$.url')),
	TRIM(BOTH '"' FROM JSON_EXTRACT(posts.meta, '$.size')),
	'draft',
	posts.datecreated
FROM
	posts
WHERE posts.type = 'attachment';



