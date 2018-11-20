<?php
namespace dash\app\log\caller;

class envterVerificationCode
{
	public static function text($_code = null)
	{
		return T_("Your verification code is :mycode", ['mycode' => $_code]);
	}

	public static function send_to_creator()
	{
		return true;
	}

	public static function is_notif()
	{
		return true;
	}

}
?>