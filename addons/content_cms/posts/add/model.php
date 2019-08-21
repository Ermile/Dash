<?php
namespace content_cms\posts\add;


class model
{
	public static function post()
	{

		$post =
		[
			'title'       => \dash\request::post('title'),
			'content'     => isset($_POST['content']) ? $_POST['content'] : null,
		];

		if(\dash\url::subdomain())
		{
			$post['subdomain'] = \dash\url::subdomain();
		}

		if(\dash\request::get('type'))
		{
			$post['type'] = \dash\request::get('type');
		}

		$post_detail = \dash\app\posts::add($post);

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
