<?php
namespace addons\content_enter\username\change;

class controller extends \addons\content_enter\main\controller
{
	/**
	 * check route of account
	 * @return [type] [description]
	 */
	public function ready()
	{
		// if the user is login redirect to base
		parent::if_login_route();

		// if the user have not an email can not change her email
		// he must set email
		if(!$this->login('username'))
		{
			$this->redirector($this->url('base'). '/enter/username/set')->redirect();
			return;
		}

		// parent::ready();
		$this->get()->ALL('username/change');
		$this->post('username')->ALL('username/change');


	}
}
?>