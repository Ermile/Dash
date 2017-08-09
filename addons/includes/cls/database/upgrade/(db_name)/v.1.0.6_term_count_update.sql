-- multi_query
CREATE TRIGGER `update_count` AFTER UPDATE ON `termusages` FOR EACH ROW BEGIN
-- plus new term count
UPDATE
	terms
SET
	terms.term_count = IF(terms.term_count IS NULL OR terms.term_count = '', 1, terms.term_count + 1)
WHERE
	terms.id = NEW.term_id
LIMIT 1;
-- minus old term count
UPDATE
	terms
SET
	terms.term_count = IF(terms.term_count IS NULL OR terms.term_count = '', 0, terms.term_count - 1)
WHERE
	terms.id = OLD.term_id
LIMIT 1;

END