<?php
namespace addons\content_cp\terms;


class controller extends \addons\content_cp\main\controller
{
	public function ready()
	{

		$this->post('terms')->ALL();
		if(\lib\request::get('edit'))
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
