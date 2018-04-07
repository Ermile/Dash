<?php
namespace addons\content_enter\pass\set;

class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{
		// if this step is locked go to error page and return
		if(\dash\utility\enter::lock('pass/set'))
		{
			\dash\header::status(404, 'pass/set');
			return;
		}

		// if step mobile is done
		if(self::done_step('mobile') && !\dash\utility\enter::user_data('password'))
		{
			// parent::ready();
			$this->get('pass')->ALL('pass/set');
			$this->post('pass')->ALL('pass/set');
		}
		else
		{
			// make page error or redirect
			\dash\header::status(404, 'pass/set');
		}
	}
}
?>