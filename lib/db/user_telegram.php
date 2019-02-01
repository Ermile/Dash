<?php
namespace dash\db;


class user_telegram
{

	public static function insert()
	{
		return \dash\db\config::public_insert('user_telegram', ...func_get_args());
	}


	public static function multi_insert()
	{
		return \dash\db\config::public_multi_insert('user_telegram', ...func_get_args());
	}


	public static function update()
	{
		return \dash\db\config::public_update('user_telegram', ...func_get_args());
	}


	public static function get()
	{
		return \dash\db\config::public_get('user_telegram', ...func_get_args());
	}

	public static function get_count()
	{
		return \dash\db\config::public_get_count('user_telegram', ...func_get_args());
	}


	public static function search()
	{
		$result = \dash\db\config::public_search('user_telegram', ...func_get_args());
		return $result;
	}

}
?>
