<?php
namespace content_crm\member\social;


class model
{


	public static function getPost()
	{
		$post =
		[

			'website'     => \dash\request::post('website'),
			'instagram'   => \dash\request::post('instagram'),
			'linkedin'    => \dash\request::post('linkedin'),
			'facebook'    => \dash\request::post('facebook'),
			'twitter'     => \dash\request::post('twitter'),
			'email'       => \dash\request::post('email'),
		];


		return $post;
	}


	/**
	 * Posts a user add.
	 */
	public static function post()
	{

		$request = self::getPost();

		$result = \dash\app\user::edit($request, \dash\request::get('id'));

		if(\dash\engine\process::status())
		{
			\dash\redirect::pwd();
		}
	}
}
?>