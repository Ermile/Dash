<?php
namespace content_su\users\edit;


class model
{

	public static function getPostUser()
	{
		$post =
		[
			'firstname'            => \dash\request::post('name'),
			'lastname'             => \dash\request::post('lastName'),
			'mobile'               => \dash\request::post('mobile'),
			'nationalcode'         => \dash\request::post('nationalcode'),
			'father'               => \dash\request::post('father'),
			'birthday'             => \dash\request::post('birthday'),
			'gender'               => \dash\request::post('gender'),
			'marital'              => \dash\request::post('marital'),
			'child'                => \dash\request::post('child'),
			'birthcity'            => \dash\request::post('birthcity'),
			'shfrom'               => \dash\request::post('shfrom'),
			'shcode'               => \dash\request::post('shcode'),
			'education'            => \dash\request::post('education'),
			'job'                  => \dash\request::post('job'),
			'passportcode'         => \dash\request::post('passportcode'),
			'passportexpire'       => \dash\request::post('passportexpire'),
			'paymentaccountnumber' => \dash\request::post('paymentaccountnumber'),
			'shaba'                => \dash\request::post('shaba'),
		];

		return $post;
	}


	/**
	 * Posts a teacher add.
	 */
	public static function post()
	{
		// ready request
		$request = self::getPostUser();

		\dash\app\user::edit($request, \dash\request::get('id'));

		if(\dash\engine\process::status())
		{
			\dash\redirect::pwd();
		}
	}
}
?>
