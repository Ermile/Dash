<?php
namespace dash\db\tickets;


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
				DATE(tickets.datecreated) AS `date`
			FROM
				tickets
			WHERE
				tickets.type = 'ticket' AND
				tickets.datecreated <= '$start' AND
				tickets.datecreated >= '$end'
			GROUP BY
				DATE(tickets.datecreated)
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
				DATE(tickets.datecreated) AS `date`
			FROM
				tickets
			WHERE
				tickets.type = 'ticket' AND
				tickets.parent IS NULL AND
				tickets.datecreated <= '$start' AND
				tickets.datecreated >= '$end'
			GROUP BY
				DATE(tickets.datecreated)
			ORDER BY DATE(tickets.datecreated) ASC
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
				DATE(tickets.datecreated) AS `date`
			FROM
				tickets
			WHERE
				tickets.type = 'ticket' AND
				tickets.datecreated <= '$start' AND
				tickets.datecreated >= '$end'
			GROUP BY
				DATE(tickets.datecreated)
			ORDER BY DATE(tickets.datecreated) ASC
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
				(AVG(tickets.answertime) / 60) AS `count`,
				DATE(tickets.datecreated) AS `date`
			FROM
				tickets
			WHERE
				tickets.type = 'ticket' AND
				tickets.parent IS NULL AND
				tickets.datecreated <= '$start' AND
				tickets.datecreated >= '$end'
			GROUP BY
				DATE(tickets.datecreated)
			ORDER BY DATE(tickets.datecreated) ASC
		";
		$result = \dash\db::get($query);

		return $result;
	}

}
?>