<?php
namespace addons\content_su\tools\translation;

class controller extends \addons\content_su\main\controller
{
	public function ready()
	{
		if(\lib\url::isLocal())
		{
			// dont chcek permission on local
		}
		else
		{
			parent::ready();
		}

		$this->getUpdates();
		$this->get()->ALL();
	}

	public function getUpdates()
	{

		$exist    = true;
		$mypath   = \lib\request::get('path');
		$myupdate = \lib\request::get('update');
		if($mypath)
		{
			echo \lib\utility\twigTrans::extract($mypath, $myupdate);
			\lib\code::exit();
		}
	}
}
?>