<?php
namespace content_api\v5\user;


class user_add
{
	public static function add()
	{
		$field    = ['model', 'serial', 'manufacturer', 'version'];
		$post     = \dash\request::post();
		$add_user = [];
		$meta     = [];
		$i        = 0;

		foreach ($post as $key => $value)
		{
			$myField = mb_strtolower($key);

			if(in_array($myField, $field))
			{
				$add_user[$myField] = $value;
			}
			else
			{
				$meta[$myField] = $value;
			}
		}

		$user_add['android_meta'] = $meta;
		$token                    = json_encode($user_add);
		$token                    = md5($token);

		\dash\notif::result(['usertoken' => $token]);
		\dash\code::end();


	}
}
?>