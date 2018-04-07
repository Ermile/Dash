<?php
namespace content_su\session;


class view extends \addons\content_su\main\view
{
	public function config()
	{
		parent::config();
		$this->data->my_session = $_SESSION;
	}
}
?>