<?php
namespace content_pay\verify;


class controller
{
	public static function routing()
	{
		$bank = \dash\url::child();
		$token = \dash\url::subchild();

		if($bank && $token && mb_strlen($token) === 32)
		{
			\dash\utility\pay\verify::verify($bank, $token);
		}
	}
}
?>