<?php
namespace addons\content_su\sendnotify;

class view extends \addons\content_su\main\view
{
	public function config()
	{
		parent::config();
		$mobile_or_id = \lib\utility::get('user');
		if($mobile_or_id)
		{
			$this->data->user_info = $this->model()->connection_way($mobile_or_id);
		}
	}
}
?>