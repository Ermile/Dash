<?php
namespace dash\db\comments;


class report
{
	public static function last_month_count()
	{
		$start = date("Y-m-d H:i:s");
		$end   = date("Y-m-d H:i:s", strtotime("-365 days"));

		$query =
		"
			SELECT
				COUNT(*) AS `count`,
				DATE(comments.datecreated) AS `date`
			FROM
				comments
			WHERE
				comments.type = 'ticket' AND
				comments.datecreated <= '$start' AND
				comments.datecreated >= '$end'
			GROUP BY
				DATE(comments.datecreated)
		";
		$result = \dash\db::get($query);
		return $result;


	}

	public static function count_ticket()
	{
		$start = date("Y-m-d H:i:s");
		$end   = date("Y-m-d H:i:s", strtotime("-365 days"));

		$query =
		"
			SELECT
				COUNT(*) AS `count`,
				DATE(comments.datecreated) AS `date`
			FROM
				comments
			WHERE
				comments.type = 'ticket' AND
				comments.parent IS NULL AND
				comments.datecreated <= '$start' AND
				comments.datecreated >= '$end'
			GROUP BY
				DATE(comments.datecreated)
			ORDER BY DATE(comments.datecreated) ASC
		";
		$result = \dash\db::get($query);
		return $result;
	}

	public static function count_message()
	{
		$start = date("Y-m-d H:i:s");
		$end   = date("Y-m-d H:i:s", strtotime("-365 days"));

		$query =
		"
			SELECT
				COUNT(*) AS `count`,
				DATE(comments.datecreated) AS `date`
			FROM
				comments
			WHERE
				comments.type = 'ticket' AND
				comments.parent IS NOT NULL AND
				comments.datecreated <= '$start' AND
				comments.datecreated >= '$end'
			GROUP BY
				DATE(comments.datecreated)
			ORDER BY DATE(comments.datecreated) ASC
		";
		$result = \dash\db::get($query);
		return $result;
	}

	public static function avg_time()
	{
		$start = date("Y-m-d H:i:s");
		$end   = date("Y-m-d H:i:s", strtotime("-365 days"));

		$query =
		"
			SELECT
				AVG(comments.answertime) AS `count`,
				DATE(comments.datecreated) AS `date`
			FROM
				comments
			WHERE
				comments.type = 'ticket' AND
				comments.parent IS NULL AND
				comments.datecreated <= '$start' AND
				comments.datecreated >= '$end'
			GROUP BY
				DATE(comments.datecreated)
			ORDER BY DATE(comments.datecreated) ASC
		";
		$result = \dash\db::get($query);
		return $result;
	}

}
?>