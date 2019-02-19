<?php
namespace content_api\v5\smile;


class controller
{
	public static function routing()
	{
		\content_api\controller::check_authorization3_v5();

		$smile = self::smile();

		\content_api\controller::end5($smile);
	}


	private static function smile()
	{
		$smile     = [];

		$usercode = \dash\request::post('usercode');


		if(!$usercode)
		{
			return false;
		}

		$id = \dash\coding::decode($usercode);

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