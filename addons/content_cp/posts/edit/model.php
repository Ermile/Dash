<?php
namespace content_cp\posts\edit;

class model extends \addons\content_cp\posts\main\model
{
	public function post_edit_post()
	{

		$posts = self::getPost();

		if(!$posts || !\dash\engine\process::status())
		{
			return false;
		}

		$post_detail = \dash\app\posts::edit($posts);

		if(\dash\engine\process::status())
		{
			\dash\redirect::pwd();
		}
	}
}
?>
