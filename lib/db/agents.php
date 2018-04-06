<?php
namespace dash\db;

/** agents managing **/
class agents
{
	/**
	 * insert new agetn in database and return id of it
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function insert()
	{
		\lib\db\config::public_insert('agents', ...func_get_args());
		return \lib\db::insert_id();
	}


	/**
	 * get agent query
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get()
	{
		return \lib\db\config::public_get('agents', ...func_get_args());
	}
}
?>
