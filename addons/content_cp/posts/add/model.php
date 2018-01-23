<?php
namespace addons\content_cp\posts\add;


class model extends \mvc\model
{
	public function post_add_posts()
	{
		$post =
		[
			'title'          => \lib\utility::post('title'),
			'tag'            => \lib\utility::post('tag'),
			'slug'           => \lib\utility::post('slug'),
			'content'        => \lib\utility::post('content'),
			'publishdate'    => \lib\utility::post('publishdate'),
			'status'         => \lib\utility::post('status'),
			'comment'        => \lib\utility::post('comment'),
			'language'       => \lib\utility::post('language'),
			'type'           => \lib\utility::get('type'),
			'language'       => \lib\define::get_language(),
		];


		$all_post = \lib\utility::post();
		$post['cat'] = [];

		foreach ($all_post as $key => $value)
		{
			if(substr($key, 0, 4) === 'cat_')
			{
				$post['cat'][] = substr($key, 4);
			}
		}

		$post_detail = \lib\app\posts::add($post);

		if(\lib\debug::$status && isset($post_detail['post_id']))
		{
			$this->redirector($this->url('baseFull'). '/posts/edit?id='. $post_detail['post_id']);
			return;
		}

		if(\lib\debug::$status)
		{
			$this->redirector($this->url('full'));
		}
	}
}
?>
