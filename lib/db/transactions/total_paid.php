<?php
namespace dash\db\transactions;

trait total_paid
{

	public static function total_paid($_where = null)
	{
		$where = \dash\db\config::make_where($_where);
		if(!$where)
		{
			$where = null;
		}
		else
		{
			$where = " AND ". $where;
		}

		$query =
		"
			SELECT
				SUM(transactions.plus) AS `total`
			FROM
				transactions
			WHERE
				transactions.verify = 1
				$where
		";
		return \dash\db::get($query, 'total', true);
	}


	public static function total_paid_date($_date, $_where = null)
	{
		$where = \dash\db\config::make_where($_where);

		if(!$where)
		{
			$where = null;
		}
		else
		{
			$where = " AND ". $where;
		}
		$query =
		"
			SELECT
				SUM(transactions.plus) AS `total`
			FROM
				transactions
			WHERE
				transactions.verify = 1 AND
				DATE(transactions.datecreated) = DATE('$_date')
				$where

		";

		return \dash\db::get($query, 'total', true);
	}


	public static function total_paid_count($_where = null)
	{
		$where = \dash\db\config::make_where($_where);
		if(!$where)
		{
			$where = null;
		}
		else
		{
			$where = " AND ". $where;
		}

		$query =
		"
			SELECT
				COUNT(*) AS `total`
			FROM
				transactions
			WHERE
				transactions.verify = 1
				$where
		";
		return \dash\db::get($query, 'total', true);
	}
}
?>