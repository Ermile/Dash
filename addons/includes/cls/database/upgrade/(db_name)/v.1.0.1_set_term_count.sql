-- multi_query
CREATE TRIGGER `set_count` AFTER INSERT ON `termusages` FOR EACH ROW BEGIN
UPDATE
	terms
SET
	terms.term_count = IF(terms.term_count IS NULL OR terms.term_count = '', 1, terms.term_count + 1)
WHERE
	terms.id = NEW.term_id;
END