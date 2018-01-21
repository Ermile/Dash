<?php
namespace addons\content_cp\posts\edit;

class controller extends \addons\content_cp\main\controller
{

	public function ready()
	{

		\lib\permission::access('cp:posts:invoices', 'block');

		$id = \lib\utility::get('id');

		if(!$id || !\lib\utility\shortURL::is($id))
		{
			\lib\error::page(T_("Invalid id"));
		}

		$this->get()->ALL();

		$this->post('edit_post')->ALL();

	}
}
?>