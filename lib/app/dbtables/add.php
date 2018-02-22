<?php
namespace lib\app\dbtables;

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
		\lib\app::variable($_args);

		if(!\lib\user::id())
		{
			\lib\debug::error(T_(":user not found"), 'user');
			return false;
		}

		// check args
		$args = self::check();

		$dbtables_id = \lib\db\config::public_insert(self::$tables, $args);

		if(\lib\debug::$status)
		{
			\lib\debug::true(T_("Record successfuly added"));
		}
	}
}
?>