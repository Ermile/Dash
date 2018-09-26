<?php
namespace dash\app\dbtables;

trait add
{

	/**
	 * add new dbtables
	 *
	 * @param      array          $_args  The arguments
	 *
	 * @return     array|boolean  ( description_of_the_return_value )
	 */
	public static function add($_args = [])
	{
		\dash\app::variable($_args);

		if(!\dash\user::id())
		{
			\dash\notif::error(T_(":user not found"), 'user');
			return false;
		}

		// check args
		$args = self::check();
		\dash\log::set('addDataTabelRaw');
		$dbtables_id = \dash\db\config::public_insert(self::$tables, $args);

		if(\dash\engine\process::status())
		{
			\dash\notif::ok(T_("Record successfuly added"));
		}
	}
}
?>