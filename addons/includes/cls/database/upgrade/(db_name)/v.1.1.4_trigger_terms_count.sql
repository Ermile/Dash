-- multi_query
CREATE TRIGGER `termusages_change_terms_count_usercount_on_insert` AFTER INSERT ON `termusages` FOR EACH ROW
BEGIN
	IF(NEW.termusage_foreign = 'posts') THEN
		UPDATE terms SET terms.term_count = IF(terms.term_count IS NULL, 1, terms.term_count + 1) WHERE	terms.id = NEW.term_id LIMIT 1;
	END IF;

	IF(NEW.termusage_foreign = 'users') THEN
		UPDATE terms SET terms.term_usercount = IF(terms.term_usercount IS NULL, 1, terms.term_usercount + 1) WHERE	terms.id = NEW.term_id LIMIT 1;
	END IF;
END