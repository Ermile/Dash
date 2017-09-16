<?php
namespace addons\content_su\shorturl;
use \lib\utility;

class view extends \addons\content_su\main\view
{
	public function config()
	{
		parent::config();

		$val = utility::get('val');

		if($val)
		{
			$this->data->val_decode = utility\shortURL::decode($val);
			$this->data->val_encode = utility\shortURL::encode($val);
		}

	}
}
?>