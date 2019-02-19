<?php
namespace dash\db;


class user_auth
{

	public static function insert()
	{
		return \dash\db\config::public_insert('user_auth', ...func_get_args());
	}


	public static function multi_insert()
	{
		return \dash\db\config::public_multi_insert('user_auth', ...func_get_args());
	}


	public static function update()
	{
		return \dash\db\config::public_update('user_auth', ...func_get_args());
	}


	public static function update_where()
	{
		return \dash\db\config::public_update_where('user_auth', ...func_get_args());
	}


	public static function get()
	{
		return \dash\db\config::public_get('user_auth', ...func_get_args());
	}

	public static function get_count()
	{
		return \dash\db\config::public_get_count('user_auth', ...func_get_args());
	}


	public static function search()
	{
		$result = \dash\db\config::public_search('user_auth', ...func_get_args());
		return $result;
	}


	public static function hard_delete($_id)
	{
		if(!$_id || !is_numeric($_id))
		{
			return false;
		}

		$query = "DELETE FROM user_auth WHERE user_auth.id = $_id LIMIT 1";
		return \dash\db::query($query);
	}

}
?>
