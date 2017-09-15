<?php
namespace addons\content_cp\users\edit;

class view extends \addons\content_cp\main\view
{
	public function view_edit($_args)
	{
		$this->data->edit_mode = true;
		$this->data->user_detail = $this->model()->getUserDetail($_args);
	}
}
?>