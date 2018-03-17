<?php
namespace addons\content_cp\posts\add;


class model extends \addons\content_cp\posts\main\model
{
	public function post_add_posts()
	{

		$posts = self::getPost();

		if(!$posts || !\lib\notif::$status)
		{
			return false;
		}

		$post_detail = \lib\app\posts::add($posts);

		if(\lib\notif::$status && isset($post_detail['post_id']))
		{
			\lib\redirect::to(\lib\url::here(). '/posts/edit?id='. $post_detail['post_id']);
			return;
		}

		if(\lib\notif::$status)
		{
			\lib\redirect::pwd();
		}
	}
}
?>
