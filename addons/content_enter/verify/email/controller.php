<?php
namespace content_enter\verify\email;


class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{

		// if this step is locked go to error page and return
		if(\dash\utility\enter::lock('verify/email'))
		{
			\dash\header::status(404, 'verify/email');
			return;
		}

		$this->get()->ALL('verify/email');
	}
}
?>