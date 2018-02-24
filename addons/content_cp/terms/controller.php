<?php
namespace addons\content_cp\terms;


class controller extends \addons\content_cp\main\controller
{
	public function ready()
	{

		$this->post('terms')->ALL();
		if(\lib\utility::get('edit'))
		{
			$this->get(false, 'edit')->ALL();
		}
		else
		{
			$this->get()->ALL();
		}
	}
}
?>
