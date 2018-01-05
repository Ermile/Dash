<?php
namespace lib\app;

class transaction
{
	use \lib\app\transaction\datalist;

	public static function total_paid($_unit = null)
	{
		$total_paid = \lib\db\transactions::total_paid($_unit);
		return intval($total_paid);
	}


	public static function total_paid_date($_date)
	{
		$total_paid = \lib\db\transactions::total_paid_date($_date);
		return intval($total_paid);
	}
}
?>