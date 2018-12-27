<?php
namespace content_crm\member\education;


class model
{

	public static function post()
	{
		$post                    = [];
		$post['education']       = \dash\request::post('education');
		$post['educationcourse'] = \dash\request::post('educationcourse');

		\dash\app\user::edit($post, \dash\request::get('id'));

		if(\dash\engine\process::status())
		{
			\dash\redirect::pwd();
		}
	}
}
?>
