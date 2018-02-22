<?php
namespace addons\content_cp\comments\edit;

class controller extends \addons\content_cp\main\controller
{

	public function ready()
	{

		$id = \lib\utility::get('id');

		if(!$id || !\lib\utility\shortURL::is($id))
		{
			\lib\error::page(T_("Invalid id"));
		}

		$this->get()->ALL();

		$this->post('change_status')->ALL();

	}
}
?>