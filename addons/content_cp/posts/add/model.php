<?php
namespace content_cp\posts\add;


class model extends \addons\content_cp\posts\main\model
{
	public function post()
	{

		$posts = self::getPost();

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
