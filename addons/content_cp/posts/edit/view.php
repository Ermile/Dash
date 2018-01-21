<?php
namespace addons\content_cp\posts\edit;

class view extends \addons\content_cp\main\view
{
	public function config()
	{
		parent::config();

		$id = \lib\utility::get('id');

		$detail = \lib\app\posts::get($id);
		if(!$detail)
		{
			\lib\error::access(T_("Invalid id"));
		}

		$this->data->dataRaw = $detail;
		$this->data->cat_list = \lib\app\term::cat_list();
	}
}
?>