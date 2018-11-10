<?php
namespace dash\db;


class address
{

	public static function insert()
	{
		return \dash\db\config::public_insert('address', ...func_get_args());
	}


	public static function multi_insert()
	{
		return \dash\db\config::public_multi_insert('address', ...func_get_args());
	}


	public static function update()
	{
		return \dash\db\config::public_update('address', ...func_get_args());
	}


	public static function get()
	{
		return \dash\db\config::public_get('address', ...func_get_args());
	}

	public static function get_count()
	{
		return \dash\db\config::public_get_count('address', ...func_get_args());
	}


	public static function search()
	{
		$result = \dash\db\config::public_search('address', ...func_get_args());
		return $result;
	}

}
?>
