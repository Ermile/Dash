<?php
namespace addons\content_cp\posts\edit;

class model extends \addons\content_cp\posts\main\model
{
	public function post_edit_post()
	{

		$posts = self::getPost();

		if(!$posts || !\lib\notif::$status)
		{
			return false;
		}

		$post_detail = \lib\app\posts::edit($posts);

		if(\lib\notif::$status)
		{
			\lib\redirect::pwd();
		}
	}
}
?>
