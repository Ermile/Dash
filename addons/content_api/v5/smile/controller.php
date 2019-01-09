<?php
namespace content_api\v5\smile;


class controller
{
	public static function routing()
	{
		\content_api\controller::check_authorization_v5();

		$smile = self::smile();

		\dash\code::jsonBoom($smile);
	}


	private static function smile()
	{
		$smile     = [];

		$user_code = \dash\request::post('user_code');


		if(!$user_code)
		{
			return false;
		}

		$id = \dash\coding::decode($user_code);

		if(!$id)
		{
			return false;
		}

		$notif_count = \dash\app\log::my_notif_count($id);

		$smile =
		[
			'notif_new'   => $notif_count ? true : false,
			'notif_count' => $notif_count,
		];

		return $smile;
	}
}
?>