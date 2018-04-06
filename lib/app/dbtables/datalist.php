<?php
namespace dash\app\dbtables;

trait datalist
{


	public static function sort_field()
	{
		return self::get_field();
	}


	/**
	 * Gets the dbtables.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The dbtables.
	 */
	public static function list($_string = null, $_args = [])
	{
		if(!\dash\user::id())
		{
			return false;
		}

		$default_meta =
		[
			'sort'  => null,
			'order' => null,
		];

		if(!is_array($_args))
		{
			$_args = [];
		}

		$_args = array_merge($default_meta, $_args);

		if($_args['sort'] && !in_array($_args['sort'], self::sort_field()))
		{
			$_args['sort'] = null;
		}

		$result            = \dash\db\config::public_search(self::$table, $_string, $_args);
		return $result;
	}
}
?>