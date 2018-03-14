<?php
namespace addons\content_cp\posts\add;


class model extends \addons\content_cp\posts\main\model
{
	public function post_add_posts()
	{

		$posts = self::getPost();

		if(!$posts || !\lib\debug::$status)
		{
			return false;
		}

		$post_detail = \lib\app\posts::add($posts);

		if(\lib\debug::$status && isset($post_detail['post_id']))
		{
			$this->redirector($this->url('baseFull'). '/posts/edit?id='. $post_detail['post_id']);
			return;
		}

		if(\lib\debug::$status)
		{
			$this->redirector(\lib\url::pwd());
		}
	}
}
?>
