<?php
namespace lib\db\transactions;

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
		return \lib\db::get($query, 'total', true);
	}
}
?>