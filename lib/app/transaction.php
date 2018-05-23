<?php
namespace dash\app;

class transaction
{
	use \dash\app\transaction\datalist;

	public static function total_paid($_unit = null)
	{
		$total_paid = \dash\db\transactions::total_paid($_unit);
		return intval($total_paid);
	}

	public static function total_paid_count()
	{
		$total_paid = \dash\db\transactions::total_paid_count();
		return intval($total_paid);
	}


	public static function total_paid_date($_date)
	{
		$total_paid = \dash\db\transactions::total_paid_date($_date);
		return intval($total_paid);
	}
}
?>