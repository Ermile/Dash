<?php
namespace content_su\dbtables;
class controller extends \addons\content_su\main\controller
{
	public function ready()
	{
		parent::ready();
		$this->post('dbtables')->ALL();
	}
}
?>