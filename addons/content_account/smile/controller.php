<?php
namespace content_account\smile;


class controller
{

	public static function routing()
	{
		$result =
		[
			'ok'       => true,
			'newNotif' => true,
		];

		\dash\code::jsonBoom($result);
	}
}
?>