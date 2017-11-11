<?php
namespace lib\db;


/** contacts managing **/
class contacts
{

	/**
	 * insert new tag in contacts table
	 * @param array $_args fields data
	 * @return mysql result
	 */
	public static function insert()
	{
		\lib\db\config::public_insert('contacts', ...func_get_args());
	}


	/**
	 * insert multi value to contacts
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function insert_multi()
	{
		return \lib\db\config::public_multi_insert('contacts', ...func_get_args());
	}


	/**
	 * update field from contacts table
	 * get fields and value to update
	 * @example update table set field = 'value' , field = 'value' , .....
	 * @param array $_args fields data
	 * @param string || int $_id record id
	 * @return mysql result
	 */
	public static function update()
	{
		return \lib\db\config::public_update('contacts', ...func_get_args());
	}


	/**
	 * update record by where condition
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function update_where()
	{
		return \lib\db\config::public_update_where('contacts', ...func_get_args());
	}


	/**
	 * get the contacts by id
	 *
	 * @param      <type>  $_contact_id  The contact identifier
	 * @param      string  $_field    The field
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get()
	{
		return \lib\db\config::public_get('contacts', ...func_get_args());
	}


	/**
	 * Searches for the first match.
	 *
	 * @param      <type>  $_title  The title
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function search()
	{
		return \lib\db\config::public_search('contacts', ...func_get_args());
	}
}
?>
