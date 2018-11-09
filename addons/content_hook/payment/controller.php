<?php
namespace content_hook\payment;


class controller
{
	public static function routing()
	{
		$url             = \dash\url::directory();

		$url_type        = \dash\url::child();
		$payment         = \dash\url::subchild();

		$args            = [];
		$args['get']     = \dash\request::get();
		$args['post']    = \dash\request::post();
		$args['request'] = \dash\safe::safe($_REQUEST);

		switch ($url_type)
		{
			case 'verify':
				if(is_callable(["\\dash\\utility\\payment\\verify\\$payment", $payment]))
				{
					// set default timeout for socket
        			ini_set("default_socket_timeout", 10);

					\dash\log::set('paymentVerifyCall');
					("\\dash\\utility\\payment\\verify\\$payment")::$payment($args);
					return;
				}
				break;

			default:
				\dash\header::status(404, T_("Invalid payment type"));
				break;
		}
	}
}
?>