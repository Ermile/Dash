<?php
namespace content_crm\member\education;


class model
{

	public static function post()
	{
		\dash\permission::access('aMemberEdit');

		$post                    = [];
		$post['education']       = \dash\request::post('education');
		$post['educationcourse'] = \dash\request::post('educationcourse');

		\dash\app\member::edit($post, \dash\request::get('id'));

		if(\dash\engine\process::status())
		{
			\dash\redirect::pwd();
		}
	}
}
?>
