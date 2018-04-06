<?php
namespace addons\content_su\shorturl;


class view extends \addons\content_su\main\view
{
	public function config()
	{
		parent::config();

		$val = \dash\request::get('val');

		if($val)
		{
			$this->data->val_decode = \dash\coding::decode($val);
			$this->data->val_encode = \dash\coding::encode($val);
		}

	}
}
?>