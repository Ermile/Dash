<?php
namespace content_enter\pass\signup;

class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{

		// if this step is locked go to error page and return
		if(\dash\utility\enter::lock('pass/signup'))
		{
			\dash\header::status(404, 'pass/signup');
			return;
		}

		// if step mobile is done
		if(self::done_step('mobile') && !\dash\utility\enter::user_data('password'))
		{
			// parent::ready();
			$this->get('pass')->ALL('pass/signup');
			$this->post('pass')->ALL('pass/signup');
		}
		else
		{
			// make page error or redirect
			\dash\header::status(404, 'pass/signup');
		}
	}
}
?>