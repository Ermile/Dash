<?php
namespace content_enter\verify\what;


class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{

		// if this step is locked go to error page and return
		if(\dash\utility\enter::lock('verify/what'))
		{
			\dash\header::status(404, 'verify/what');
			return;
		}

		if(!self::loaded_module('verify/what'))
		{
			self::loaded_module('verify/what', true);
			if(\dash\utility\enter::get_session('verification_code_id') && is_numeric(\dash\utility\enter::get_session('verification_code_id')))
			{
				\dash\db\logs::update(['status' => 'expire'], \dash\utility\enter::get_session('verification_code_id'));
			}
		}

		$this->get()->ALL('verify/what');
	}
}
?>