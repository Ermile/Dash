<?php
namespace dash\db;
use \dash\db;

class invoice_details
{

	/**
	 * get the invoice
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get()
	{
		return \dash\db\config::public_get('invoice_details', ...func_get_args());
	}


	/**
	 * insert the new invoice
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function insert()
	{
		\dash\db\config::public_insert('invoice_details', ...func_get_args());
		return \dash\db::insert_id();
	}


	/**
	 * Searches for the first match.
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function search()
	{
		return \dash\db\config::public_search('invoice_details', ...func_get_args());
	}
}
?>
