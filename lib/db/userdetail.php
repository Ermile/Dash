<?php
namespace dash\db;


class userdetail
{


	public static function insert()
	{
		\dash\db\config::public_insert('userdetail', ...func_get_args());
	}


	public static function insert_multi()
	{
		return \dash\db\config::public_multi_insert('userdetail', ...func_get_args());
	}


	public static function update()
	{
		return \dash\db\config::public_update('userdetail', ...func_get_args());
	}


	public static function update_where()
	{
		return \dash\db\config::public_update_where('userdetail', ...func_get_args());
	}


	public static function get()
	{
		return \dash\db\config::public_get('userdetail', ...func_get_args());
	}


	public static function get_count()
	{
		return \dash\db\config::public_get_count('userdetail', ...func_get_args());
	}


	public static function search($_string = null, $_option = [])
	{
		$defalut =
		[
			'public_show_field' => " userdetail.*, cU.firstname, cU.lastname",
			'master_join' => " LEFT JOIN users AS  `cU` ON cU.id = userdetail.creator",
		];

		if(!is_array($_option))
		{
			$_option = [];
		}

		$_option = array_merge($defalut, $_option);

		return \dash\db\config::public_search('userdetail', $_string, $_option);
	}

}
?>
