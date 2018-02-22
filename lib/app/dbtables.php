<?php
namespace lib\app;

/**
 * Class for dbtables.
 */
class dbtables
{
	public static $table = null;

	use dbtables\add;
	use dbtables\edit;
	use dbtables\datalist;
	use dbtables\dashboard;

	public static function get_field()
	{
		$result = \lib\db::get("DESC ". self::$table);
		$result = array_column($result, 'Field');
		return $result;
	}

	public static function get($_id)
	{
		if(!$id)
		{
			\lib\debug::error(T_(":dbtables id not set"));
			return false;
		}

		$get = \lib\db\config::public_get(self::$table, ['id' => $id, 'school_id' => \lib\school::id(), 'limit' => 1]);

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
		foreach (\lib\app::request() as $key => $value)
		{
			$args[$key]  = $value;
		}
		return $args;
	}
}
?>