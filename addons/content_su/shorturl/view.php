<?php
namespace addons\content_su\shorturl;


class view extends \addons\content_su\main\view
{
	public function config()
	{
		parent::config();

		$val = \lib\request::get('val');

		if($val)
		{
			$this->data->val_decode = \lib\utility\shortURL::decode($val);
			$this->data->val_encode = \lib\utility\shortURL::encode($val);
		}

	}
}
?>