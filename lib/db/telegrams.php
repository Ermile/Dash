<?php
namespace dash\db;

/** telegrams managing **/
class telegrams
{

	public static function insert()
	{
		return \dash\db\config::public_insert('telegrams', ...func_get_args());
	}


	public static function multi_insert()
	{
		return \dash\db\config::public_multi_insert('telegrams', ...func_get_args());
	}


	public static function update()
	{
		return \dash\db\config::public_update('telegrams', ...func_get_args());
	}


	public static function get()
	{
		return \dash\db\config::public_get('telegrams', ...func_get_args());
	}


	public static function search()
	{
		$result = \dash\db\config::public_search('telegrams', ...func_get_args());
		return $result;
	}

}
?>
