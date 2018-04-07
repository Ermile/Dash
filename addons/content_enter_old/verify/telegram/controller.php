<?php
namespace content_enter\verify\telegram;


class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{

		// if this step is locked go to error page and return
		if(self::lock('verify/telegram'))
		{
			self::error_page('verify/telegram');
			return;
		}


		$this->get()->ALL('verify/telegram');
		$this->post('verify')->ALL('verify/telegram');

	}
}
?>