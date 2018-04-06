<?php
namespace dash\db\transactions;

trait total_paid
{

	public static function total_paid($_unit = null)
	{
		$query =
		"
			SELECT
				SUM(transactions.plus) AS `total`
			FROM
				transactions
			WHERE
				transactions.verify = 1
		";
		return \dash\db::get($query, 'total', true);
	}


	public static function total_paid_date($_date)
	{
		$query =
		"
			SELECT
				SUM(transactions.plus) AS `total`
			FROM
				transactions
			WHERE
				transactions.verify = 1 AND
				DATE(transactions.date) = DATE('$_date')

		";
		return \dash\db::get($query, 'total', true);
	}
}
?>