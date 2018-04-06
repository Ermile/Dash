<?php
namespace addons\content_cp\posts\edit;

class controller extends \addons\content_cp\main\controller
{

	public function ready()
	{

		\dash\permission::access('cp:posts:invoices', 'block');

		$id = \dash\request::get('id');

		if(!$id || !\dash\coding::is($id))
		{
			\dash\header::status(404, T_("Invalid id"));
		}

		$this->get()->ALL();

		$this->post('edit_post')->ALL();

	}
}
?>