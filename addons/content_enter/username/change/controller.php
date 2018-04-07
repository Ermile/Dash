<?php
namespace content_enter\username\change;

class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{
		// if the user is login redirect to base
		parent::if_login_route();

		// if the user have not an email can not change her email
		// he must set email
		if(!\dash\user::login('username'))
		{
			\dash\redirect::to(\dash\url::base(). '/enter/username/set');
			return;
		}

		// parent::ready();
		$this->get()->ALL('username/change');
		$this->post('username')->ALL('username/change');


	}
}
?>