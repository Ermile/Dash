<?php
namespace addons\content_cp\posts\edit;

class model extends \mvc\model
{
	public function post_edit_post()
	{
		$post =
		[
			'id'          => \lib\utility::get('id'),
			'title'       => \lib\utility::post('title'),
			'content'     => \lib\utility::post('content'),
			// 'publishdate' => \lib\utility::post('publishdate'),
			'status'      => \lib\utility::post('status'),
			'language'    => \lib\utility::post('language'),
			'type'        => 'post',
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

		$post_detail = \lib\app\posts::edit($post);

		if(\lib\debug::$status)
		{
			$this->redirector($this->url('full'));
		}
	}
}
?>
