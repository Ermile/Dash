<?php
namespace addons\content_cp\posts\add;

class view extends \addons\content_cp\main\view
{
	public function config()
	{
		parent::config();
		$this->data->cat_list = \lib\app\term::cat_list();
	}
}
?>