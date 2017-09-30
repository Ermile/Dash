<?php
namespace addons\content_enter\payment;
use \lib\debug;
use \lib\utility;

class controller extends \addons\content_enter\main\controller
{
	public function _route()
	{
		$url             = \lib\router::get_url();

		$url_type        = \lib\router::get_url(1);
		$payment         = \lib\router::get_url(2);

		$args            = [];
		$args['get']     = utility::get(null, 'raw');
		$args['post']    = utility::post();
		$args['request'] = utility\safe::safe($_REQUEST);

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
				\lib\error::page(T_("Invalid payment type"));
				break;
		}
	}
}
?>