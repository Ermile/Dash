<?php
namespace content_enter\byebye;


class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{
		// if this step is locked go to error page and return
		if(self::lock('byebye'))
		{
			self::error_page('byebye');
			return;
		}
		$this->get()->ALL('byebye');
	}
}
?>