<?php
namespace content_crm\permission\delete;


class model
{
	public static function post()
	{
		$name   = \dash\request::get('id');
		$delete = \dash\permission::delete_permission($name);
		if($delete)
		{
			\dash\log::set('permissionDelete', ['name' => $name]);
			\dash\redirect::to(\dash\url::this());
		}
	}
}
?>