<?php
namespace content_enter\payment;


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
				if(method_exists("\\dash\\utility\\payment\\verify", $payment))
				{
					\dash\log::set('paymentVerifyCall');
					\dash\utility\payment\verify::$payment($args);
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