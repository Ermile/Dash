<?php
namespace content_cp\terms;


class controller extends \content_cp\main2\controller
{
	function ready()
	{

		$this->post('service')->ALL();
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
