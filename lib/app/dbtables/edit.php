<?php
namespace dash\app\dbtables;

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

			if(\lib\engine\process::status())
			{
				\lib\notif::ok(T_("Record successfully updated"));
			}
		}
	}
}
?>