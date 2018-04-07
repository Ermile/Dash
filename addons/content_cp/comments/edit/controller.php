<?php
namespace content_cp\comments\edit;

class controller extends \addons\content_cp\main\controller
{

	public function ready()
	{

		$id = \dash\request::get('id');

		if(!$id || !\dash\coding::is($id))
		{
			\dash\header::status(404, T_("Invalid id"));
		}

		$this->get()->ALL();

		$this->post('change_status')->ALL();

	}
}
?>