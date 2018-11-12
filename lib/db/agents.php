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
	public static function insert($_args)
	{
		$result = \dash\db\config::public_insert('agents', $_args, \dash\db::get_db_log_name());

		if(\dash\db::get_db_log_name() === true)
		{
			$result = \dash\db::insert_id();
		}
		elseif(isset(\dash\db::$link_open[\dash\db::get_db_log_name()]))
		{
			$result = \dash\db::insert_id(\dash\db::$link_open[\dash\db::get_db_log_name()]);
		}
		return $result;
	}


	/**
	 * get agent query
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get($_where)
	{
		return \dash\db\config::public_get('agents', $_where, ['db_name' => \dash\db::get_db_log_name()]);
	}


	public static function get_count($_where = [])
	{
		return \dash\db\config::public_get_count('agents', $_where, \dash\db::get_db_log_name());
	}
}
?>
