<?php
namespace content_cp\permission\delete;


class model
{
	public static function post()
	{
		$name   = \dash\request::get('id');
		$delete = \dash\permission::delete_permission($name);
		if($delete)
		{
			\dash\redirect::to(\dash\url::this());
		}
	}
}
?>