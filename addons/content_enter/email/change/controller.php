<?php
namespace addons\content_enter\email\change;

class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{
		if(\lib\url::directory() === 'email/change/google')
		{
			// @check
			// \lib\engine\mvc::controller_set("\\addons\content_enter\\email\\change\\google\\controller");
			return;
		}

		// if the user is login redirect to base
		parent::if_login_route();

		// if the user have not an email can not change her email
		// he must set email
		if(!\lib\user::login('email'))
		{
			\lib\redirect::to(\lib\url::base(). '/enter/email/set');
			return;
		}

		$this->get()->ALL('email/change');
		$this->post('change')->ALL('email/change');
	}
}
?>