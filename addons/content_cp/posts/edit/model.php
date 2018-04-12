<?php
namespace content_cp\posts\edit;

class model
{
	public static function post()
	{

		$posts = \content_cp\posts\main\model::getPost();

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
