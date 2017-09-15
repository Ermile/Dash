<?php
namespace addons\content_cp\permission;

class view extends \addons\content_cp\main\view
{
	public function view_add($_args)
	{
		$list_perm = \lib\permission::list();
		$this->data->permission_list = $list_perm;
		$id = isset($_args->match->url[0][1]) ? $_args->match->url[0][1] : null;
		$id = \lib\utility\shortURL::decode($id);
		if($id)
		{
			$user_detail = \lib\db\users::get(['id' => $id, 'limit' => 1]);
			if(isset($user_detail['permission']))
			{
				$this->data->user_permission = explode(',', $user_detail['permission']);
			}
		}

	}
}
?>