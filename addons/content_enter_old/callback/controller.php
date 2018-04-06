<?php
namespace addons\content_enter\callback;


class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{
		// 10002000200251


		if(!\dash\request::get('service') || \dash\request::get('uid') != '201708111')
		{
			\dash\header::status(404, T_("Invalid url"));
		}

		switch (\dash\request::get('service'))
		{
			case 'kavenegar':
				$this->model()->kavenegar();
				break;

			default:
				\dash\header::status(404, T_("Invalid service"));
				break;
		}
	}
}
?>