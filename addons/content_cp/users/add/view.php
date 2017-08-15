<?php
namespace addons\content_cp\users\add;

class view extends \mvc\view
{
	public function view_add($_args)
	{
		if(isset($_args->api_callback))
		{
			$data = $_args->api_callback;
			if(isset($data['user_id']))
			{
				$this->data->get_mobile = \lib\db\users::get_mobile($data['user_id']);
			}
			$this->data->user_record = $data;
		}

		if(\lib\utility::get('mobile'))
		{
			$this->data->get_mobile = \lib\utility\filter::mobile(\lib\utility::get('mobile'));
		}
	}

}
?>