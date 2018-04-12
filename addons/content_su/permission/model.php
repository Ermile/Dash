<?php
namespace content_su\permission;


class model
{

	/**
	 * Posts an add.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function post()
	{
		$id = \dash\request::get('id');
		$id = \dash\coding::decode($id);
		if(!$id)
		{
			return false;
		}

		$post = \dash\request::post();
		$perm_list = [];
		foreach ($post as $key => $value)
		{
			if(preg_match("/^perm\_(\d+)$/", $key, $split))
			{
				$perm_list[] = $split[1];
			}
		}

		if(!empty($perm_list))
		{
			$perm_field = implode(',', $perm_list);
			\dash\db\users::update(['permission' => $perm_field], $id);
			\dash\notif::ok(T_("Permission added to this user"));
		}

	}
}
?>
