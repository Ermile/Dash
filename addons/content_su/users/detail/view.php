<?php
namespace addons\content_su\users\detail;

class view extends \addons\content_su\main\view
{
	public function view_detail($_args)
	{
		if(isset($_args->api_callback))
		{
			$data = $_args->api_callback;
			if(isset($data['user_id']))
			{
				$this->data->getMobile = \dash\db\users::get_mobile($data['user_id']);
			}
			$this->data->user_record = $data;
		}
	}
}
?>