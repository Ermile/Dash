<?php
namespace content_cms\posts\add;


class model
{
	public static function post()
	{
		$myType = \dash\request::get('type');
		switch ($myType)
		{
			case 'page':
				\dash\permission::access('cpPageAdd');
				break;

			case 'help':
				\dash\permission::access('cpHelpCenterAdd');
				break;

			case 'mag':
				\dash\permission::access('cpMagAdd');
				break;

			case 'post':
			default:
				\dash\permission::access('cpPostsAdd');
				break;
		}


		$posts = \content_cms\posts\main\model::getPost();

		if(!$posts || !\dash\engine\process::status())
		{
			return false;
		}

		$post_detail = \dash\app\posts::add($posts);

		if(\dash\engine\process::status() && isset($post_detail['post_id']))
		{
			\dash\redirect::to(\dash\url::here(). '/posts/edit?id='. $post_detail['post_id']);
			return;
		}

		if(\dash\engine\process::status())
		{
			\dash\redirect::pwd();
		}
	}
}
?>
