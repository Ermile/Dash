<?php
namespace addons\content_cp\posts\edit;

class model extends \addons\content_cp\posts\main\model
{
	public function post_edit_post()
	{

		$posts = self::getPost();

		if(!$posts || !\lib\debug::$status)
		{
			return false;
		}

		$post_detail = \lib\app\posts::edit($posts);

		if(\lib\debug::$status)
		{
			$this->redirector($this->url('full'));
		}
	}
}
?>
