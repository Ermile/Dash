<?php
namespace addons\content_enter\username\set;

class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{
		// if the user is login redirect to base
		parent::if_login_route();

		// if the user have not an email can not change her email
		// he must set email
		if(\dash\user::login('username'))
		{
			\dash\redirect::to(\dash\url::base(). '/enter/username/change');
			return;
		}
		// parent::ready();
		$this->get()->ALL('username/set');
		$this->post('username')->ALL('username/set');


	}
}
?>