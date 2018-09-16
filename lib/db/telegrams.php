<?php
namespace dash\db;

/** telegrams managing **/
class telegrams
{
	public static function get_db_log_name()
	{
		if(defined('db_log_name'))
		{
			return db_log_name;
		}
		else
		{
			return true;
		}
	}

	public static function insert($_args)
	{
		return \dash\db\config::public_insert('telegrams', $_args, self::get_db_log_name());
	}


	public static function multi_insert($_args)
	{
		return \dash\db\config::public_multi_insert('telegrams', $_args, self::get_db_log_name());
	}


	public static function update($_args, $_id)
	{
		return \dash\db\config::public_update('telegrams', $_args, $_id, self::get_db_log_name());
	}


	public static function get($_where, $_option = [])
	{
		return \dash\db\config::public_get('telegrams', $_where, ['db_name' => self::get_db_log_name()]);
	}


	public static function search($_string = null, $_option = [])
	{
		$default_option = ['db_name' => self::get_db_log_name()];
		if(!is_array($_option))
		{
			$_option = [];
		}

		$_option = array_merge($default_option, $_option);
		$result = \dash\db\config::public_search('telegrams', $_string, $_option);
		return $result;
	}

}
?>
