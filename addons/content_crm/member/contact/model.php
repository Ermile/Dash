<?php
namespace content_crm\member\contact;


class model
{

	public static function post()
	{
		\dash\permission::access('aMemberEdit');
		$post =
		[
			'phone'        => \dash\request::post('phone'),
			'mobile'       => \dash\request::post('mobile'),
			'fathermobile' => \dash\request::post('father-mobile'),
			'mothermobile' => \dash\request::post('mother-mobile'),
			'email'        => \dash\request::post('email'),
			'mobile2'        => \dash\request::post('mobile2'),
		];

		\dash\app\member::edit($post, \dash\request::get('id'));

		if(\dash\engine\process::status())
		{
			\dash\redirect::pwd();
		}
	}
}
?>
