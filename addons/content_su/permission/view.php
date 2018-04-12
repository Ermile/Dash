<?php
namespace content_su\permission;

class view
{
	public static function config()
	{
		$id = \dash\request::get('id');
		if(!$id)
		{
			\dash\header::status(404, T_("Id not found"));
		}

		$list_perm = \dash\permission::list();

		if(is_array($list_perm))
		{
			$cat                  = array_column($list_perm, 'cat');
			$cat                  = array_unique($cat);
			$cat                  = array_filter($cat);
			\dash\data::permCat($cat);
		}

		\dash\data::permissionList($list_perm);

		$id = \dash\request::get('id');
		$id = \dash\coding::decode($id);
		if($id)
		{
			$user_detail = \dash\db\users::get(['id' => $id, 'limit' => 1]);
			if(isset($user_detail['permission']))
			{
				\dash\data::userPermission(explode(',', $user_detail['permission']));
			}
		}

	}
}
?>