<?php
namespace addons\content_enter\callback;


class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{
		// 10002000200251


		if(!\lib\request::get('service') || \lib\request::get('uid') != '201708111')
		{
			\lib\header::status(404, T_("Invalid url"));
		}

		switch (\lib\request::get('service'))
		{
			case 'kavenegar':
				$this->model()->kavenegar();
				break;

			default:
				\lib\header::status(404, T_("Invalid service"));
				break;
		}
	}
}
?>