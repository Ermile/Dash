<?php
namespace content_hook\pay\home;


class model
{
	public static function post()
	{

		if(\dash\request::post('ok') == '1')
		{
			$args          = [];
			$args['token'] = \dash\url::child();
			$args['bank']  = \dash\request::post('bank');

			\dash\utility\pay\start::bank($args);
		}

	}
}
?>