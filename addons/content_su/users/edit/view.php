<?php
namespace addons\content_su\users\edit;

class view extends \addons\content_su\main\view
{
	public function view_edit($_args)
	{
		$this->data->edit_mode = true;
		$this->data->user_detail = $this->model()->getUserDetail($_args);
	}
}
?>