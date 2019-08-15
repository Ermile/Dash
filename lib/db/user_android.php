<?php
namespace dash\db;


class user_android
{

	public static function get_dataTable($_ids)
	{
		$query  = "SELECT user_android.user_id, MAX(user_android.uniquecode) AS `uniquecode` FROM user_android WHERE user_android.user_id IN ($_ids)  GROUP BY user_android.user_id";
		$result = \dash\db::get($query);
		return $result;
	}


	public static function insert()
	{
		return \dash\db\config::public_insert('user_android', ...func_get_args());
	}


	public static function multi_insert()
	{
		return \dash\db\config::public_multi_insert('user_android', ...func_get_args());
	}


	public static function update()
	{
		return \dash\db\config::public_update('user_android', ...func_get_args());
	}


	public static function update_where()
	{
		return \dash\db\config::public_update_where('user_android', ...func_get_args());
	}


	public static function get()
	{
		return \dash\db\config::public_get('user_android', ...func_get_args());
	}

	public static function get_count()
	{
		return \dash\db\config::public_get_count('user_android', ...func_get_args());
	}


	public static function search()
	{
		$result = \dash\db\config::public_search('user_android', ...func_get_args());
		return $result;
	}


	public static function hard_delete($_id)
	{
		if(!$_id || !is_numeric($_id))
		{
			return false;
		}

		$query = "DELETE FROM user_android WHERE user_android.id = $_id LIMIT 1";
		return \dash\db::query($query);
	}

}
?>
