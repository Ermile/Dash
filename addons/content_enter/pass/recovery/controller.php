<?php
namespace addons\content_enter\pass\recovery;

class controller extends \addons\content_enter\main\controller
{
	/**
	 * check route of account
	 * @return [type] [description]
	 */
	public function ready()
	{

		// if this step is locked go to error page and return
		if(self::lock('pass/recovery'))
		{
			self::error_page('pass/recovery');
			return;
		}

		// parent::ready();
		$this->get('pass')->ALL('pass/recovery');
		$this->post('pass')->ALL('pass/recovery');
	}
}
?>