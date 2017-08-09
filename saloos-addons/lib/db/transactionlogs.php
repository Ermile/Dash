<?php
namespace lib\db;


class transactionlogs
{

	/**
	 * insert new transaction log
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function insert()
	{
		return \lib\db\config::public_insert('transactionlogs', ...func_get_args());
	}

	/**
	 * get transaction logs
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get()
	{
		return \lib\db\config::public_get('transactionlogs', ...func_get_args());
	}

	/**
	 * Searches for the first match.
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function search()
	{
		return \lib\db\config::public_search('transactionlogs', ...func_get_args());
	}
}
?>