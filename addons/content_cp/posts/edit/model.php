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
			'type'        => 'post',
		];

		$post_detail = \lib\app\posts::edit($post);

		if(\lib\debug::$status)
		{
			$this->redirector($this->url('full'));
		}
	}
}
?>
