<?php
namespace addons\content_enter\callback;


class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{
		// 10002000200251


		if(!\lib\utility::get('service') || \lib\utility::get('uid') != '201708111')
		{
			\lib\error::page(T_("Invalid url"));
		}

		switch (\lib\utility::get('service'))
		{
			case 'kavenegar':
				$this->model()->kavenegar();
				break;

			default:
				\lib\error::page(T_("Invalid service"));
				break;
		}
	}
}
?>