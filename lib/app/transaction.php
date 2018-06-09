<?php
namespace dash\app;

class transaction
{
	use \dash\app\transaction\datalist;

	public static function total_paid($_where = null)
	{
		$total_paid = \dash\db\transactions::total_paid($_where);
		return intval($total_paid);
	}

	public static function total_paid_count($_where = null)
	{
		$total_paid = \dash\db\transactions::total_paid_count($_where);
		return intval($total_paid);
	}


	public static function total_paid_date($_date, $_where = null)
	{
		$total_paid = \dash\db\transactions::total_paid_date($_date, $_where);
		return intval($total_paid);
	}
}
?>