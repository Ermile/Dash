<?php
namespace content_enter\mobile\request;

class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{
			// if this step is locked go to error page and return
		if(self::lock('mobile/request'))
		{
			self::error_page('mobile/request');
			return;
		}

		// parent::ready();
		$this->get()->ALL('mobile/request');
		$this->post('mobile')->ALL('mobile/request');

	}
}
?>