-- multi_query
CREATE TRIGGER `logitems_count` AFTER INSERT ON `logs` FOR EACH ROW BEGIN
UPDATE
	logitems
SET
	logitems.count = IF(logitems.count IS NULL OR logitems.count = '', 1, logitems.count + 1)
WHERE
	logitems.id = NEW.logitem_id;
END