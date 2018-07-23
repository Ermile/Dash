<?php
namespace dash\app;

/**
 * Class for dbtables.
 */
class dbtables
{
	public static $table = null;

	use \dash\app\dbtables\add;
	use \dash\app\dbtables\edit;
	use \dash\app\dbtables\datalist;


	public static function get_field()
	{
		$result = \dash\db::get("DESC ". self::$table);
		$result = array_column($result, 'Field');
		return $result;
	}

	public static function get($_id)
	{
		if(!$id)
		{
			\dash\notif::error(T_(":dbtables id not set"));
			return false;
		}

		$get = \dash\db\config::public_get(self::$table, ['id' => $id, 'school_id' => \dash\school::id(), 'limit' => 1]);

		return $get;
	}


	/**
	 * check args
	 *
	 * @return     array|boolean  ( description_of_the_return_value )
	 */
	private static function check($_id = null)
	{
		$args           = [];
		foreach (\dash\app::request() as $key => $value)
		{
			$args[$key]  = $value;
		}
		return $args;
	}
}
?>