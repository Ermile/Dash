<?php
namespace lib\app\dbtables;

trait edit
{
	/**
	 * edit a dbtables
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function edit($_args, $_id)
	{
		\lib\app::variable($_args);

		$args = self::check($id);

		if(!empty($args))
		{
			$update = \lib\db\config::public_update($args, $_id);

			if(\lib\debug::$status)
			{
				\lib\debug::true(T_("Record successfully updated"));
			}
		}
	}
}
?>