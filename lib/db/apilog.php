<?php
namespace dash\db;


class apilog
{

	public static function insert($_args)
	{
		return \dash\db\config::public_insert('apilog', $_args, \dash\db::get_db_log_name());
	}


	public static function get($_where)
	{
		return \dash\db\config::public_get('apilog', $_where, ['db_name' => \dash\db::get_db_log_name()]);
	}


	public static function search($_string = null, $_args = [])
	{
		$default =
		[
			'db_name' => \dash\db::get_db_log_name(),
		];
		if(!is_array($_args))
		{
			$_args = [];
		}

		$_args = array_merge($default, $_args);

		$result = \dash\db\config::public_search('apilog', $_string, $_args);
		return $result;
	}

}
?>
