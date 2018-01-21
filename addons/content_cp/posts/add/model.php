<?php
namespace addons\content_cp\posts\add;


class model extends \mvc\model
{
	public function post_add_posts()
	{
		$post =
		[
			'title'       => \lib\utility::post('title'),
			'content'     => \lib\utility::post('content'),
			// 'publishdate' => \lib\utility::post('publishdate'),
			'status'      => \lib\utility::post('status'),
			'type'        => 'post',
		];

		$post_detail = \lib\app\posts::add($post);

		if(isset($post_detail['post_id']))
		{
			$this->redirector($this->url('baseFull'). '/posts/edit?id='. $post_detail['post_id']);
			return;
		}

		$this->redirector($this->url('full'));
	}
}
?>
