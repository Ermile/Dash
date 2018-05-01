<?php
namespace dash\utility\payment;


class transactions
{
	/**
	 * start transaction
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function start($_args)
	{
		$_args['condition'] = 'request';
		return \dash\db\transactions::set($_args);
	}


	public static function update()
	{
		return \dash\db\transactions::update(...func_get_args());
	}


	public static function calc_budget()
	{
		return \dash\db\transactions::calc_budget(...func_get_args());
	}
}
?>