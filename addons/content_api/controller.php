<?php
namespace content_api;


class controller
{
	public static $v5 = [];

	public static function routing()
	{

		switch (\dash\url::module())
		{
			case 'v5':
				self::check_authorization_v5();
				break;

			default:
				\dash\header::status(404);
				break;
		}
	}


	private static function check_authorization_v5()
	{
		$authorization = \dash\header::get('authorization');

		if(!isset($authorization))
		{
			\dash\header::status(400);
		}

		self::$v5['authorization'] = $authorization;

		$x_app_request = \dash\header::get('x-app-request');

		if(!isset($x_app_request))
		{
			\dash\header::status(401);
		}

		self::$v5['x_app_request'] = $x_app_request;

		$token = \dash\option::config('app_token', $x_app_request);

		if(!$token)
		{
			\dash\header::status(401);
		}

		if($token !== $authorization)
		{
			\dash\header::status(401);
		}

		self::$v5['app_token'] = $token;

	}




}
?>