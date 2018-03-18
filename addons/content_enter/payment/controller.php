<?php
namespace addons\content_enter\payment;


class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{
		$url             = \lib\url::directory();

		$url_type        = \lib\url::dir(1);
		$payment         = \lib\url::dir(2);

		$args            = [];
		$args['get']     = \lib\request::get(null, 'raw');
		$args['post']    = \lib\request::post();
		$args['request'] = \lib\utility\safe::safe($_REQUEST);

		$this->display = false;

		switch ($url_type)
		{
			case 'verify':
				if(method_exists("\\lib\\utility\\payment\\verify", $payment))
				{
					\lib\utility\payment\verify::$payment($args);
					return;
				}
				break;

			default:
				\lib\header::status(404, T_("Invalid payment type"));
				break;
		}
	}
}
?>