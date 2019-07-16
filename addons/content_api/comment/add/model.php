<?php
namespace content_api\comment\add;

class model
{
	public static function post()
	{
		$name = \dash\request::post('name');
		$post = [];

		if(\dash\user::id())
		{
			$name = \dash\user::detail('displayname');
		}

		$mobile = \dash\request::post('mobile');

		if(\dash\user::id())
		{
			$post['user_id'] = \dash\user::id();
		}
		else
		{
			$mobile         = \dash\user::detail('mobile');
			$post['mobile'] = $mobile;
		}

		$post['post_id'] = \dash\request::post('post_id');
		$post['star']    = \dash\request::post('star');
		$post['content'] = \dash\request::post('content');
		$post['author']  = $name;
		$post['type']    = 'comment';
		$post['via']     = 'site';
		$result          = \dash\app\comment::add($post);

		if($result)
		{
			\dash\notif::ok(T_("Your comment saved"));
		}
	}
}
?>