<?php
namespace addons\content_enter\delete;

class controller extends \addons\content_enter\main\controller
{
	/**
	 * check route of account
	 * @return [type] [description]
	 */
	public function ready()
	{
		$this->get()->ALL();
		$this->post('delete')->ALL();
	}
}
?>