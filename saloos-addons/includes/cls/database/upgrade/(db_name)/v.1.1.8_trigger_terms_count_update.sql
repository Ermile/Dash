-- multi_query
CREATE TRIGGER `termusages_change_terms_count_usercount_on_update` AFTER UPDATE ON `termusages` FOR EACH ROW
BEGIN
	-- POSTS COUNT
	IF(NEW.termusage_foreign = 'posts') THEN
		-- plus new term count
		UPDATE IGNORE terms SET terms.term_count = IF(terms.term_count IS NULL OR terms.term_count = '', 1, terms.term_count + 1) WHERE terms.id = NEW.term_id LIMIT 1;
		-- minus old term count
		UPDATE IGNORE terms SET terms.term_count = IF(terms.term_count IS NULL OR terms.term_count = '' OR terms.term_count < 1, 0, terms.term_count - 1) WHERE terms.id = OLD.term_id LIMIT 1;
	END IF;
	-- USERS COUNT
	IF(NEW.termusage_foreign = 'users') THEN
		-- plus new term count
		UPDATE IGNORE terms SET terms.term_usercount = IF(terms.term_usercount IS NULL OR terms.term_usercount = '', 1, terms.term_usercount + 1) WHERE terms.id = NEW.term_id LIMIT 1;
		-- minus old term count
		UPDATE IGNORE terms SET terms.term_usercount = IF(terms.term_usercount IS NULL OR terms.term_usercount = '' OR terms.term_usercount < 1, 0, terms.term_usercount - 1) WHERE terms.id = OLD.term_id LIMIT 1;
END IF;
END