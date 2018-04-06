<?php
namespace addons\content_enter\verify\sms;


class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{

		// if this step is locked go to error page and return
		if(self::lock('verify/sms'))
		{
			self::error_page('verify/sms');
			return;
		}

		$this->get()->ALL('verify/sms');
		$this->post('verify')->ALL('verify/sms');

	}
}
?>