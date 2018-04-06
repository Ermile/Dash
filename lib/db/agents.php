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
		\dash\db\config::public_insert('agents', ...func_get_args());
		return \dash\db::insert_id();
	}


	/**
	 * get agent query
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get()
	{
		return \dash\db\config::public_get('agents', ...func_get_args());
	}
}
?>
