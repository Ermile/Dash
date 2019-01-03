<?php
namespace content_hook\pay\verify;


class controller
{
	public static function routing()
	{
		$bank = \dash\url::subchild();
		$token = \dash\url::dir(3);

		if($bank && $token && mb_strlen($token) === 32)
		{
			\dash\utility\pay\verify::verify($bank, $token);
		}
	}
}
?>