<?php
namespace addons\content_enter\payment;


class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{
		$url             = \dash\url::directory();

		$url_type        = \dash\url::dir(1);
		$payment         = \dash\url::dir(2);

		$args            = [];
		$args['get']     = \dash\request::get();
		$args['post']    = \dash\request::post();
		$args['request'] = \dash\safe::safe($_REQUEST);

		$this->display = false;

		switch ($url_type)
		{
			case 'verify':
				if(method_exists("\\dash\\utility\\payment\\verify", $payment))
				{
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