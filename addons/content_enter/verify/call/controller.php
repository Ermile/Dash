<?php
namespace content_enter\verify\call;


class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{
		// if this step is locked go to error page and return
		if(\dash\utility\enter::lock('verify/call'))
		{
			\dash\header::status(404, 'verify/call');
			return;
		}

		// check method
		$this->get()->ALL('verify/call');
		$this->post('verify')->ALL('verify/call');
	}
}
?>