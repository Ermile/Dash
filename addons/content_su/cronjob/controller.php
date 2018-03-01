<?php
namespace addons\content_su\cronjob;
class controller extends \addons\content_su\main\controller
{
	public function ready()
	{
		parent::ready();
		$this->post('cronjob')->ALL();
	}
}
?>