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
		\dash\app::variable($_args);

		$args = self::check($id);

		if(!empty($args))
		{
			\dash\log::set('editDataTabelRaw');
			$update = \dash\db\config::public_update($args, $_id);

			if(\dash\engine\process::status())
			{
				\dash\notif::ok(T_("Record successfully updated"));
			}
		}
	}
}
?>