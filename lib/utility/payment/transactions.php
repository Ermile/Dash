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
}
?>