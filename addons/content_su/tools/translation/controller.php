<?php
namespace content_su\tools\translation;

class controller extends \addons\content_su\main\controller
{
	public function ready()
	{
		if(\dash\url::isLocal())
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
		$mypath   = \dash\request::get('path');
		$myupdate = \dash\request::get('update');
		if($mypath)
		{
			echo \dash\utility\twigTrans::extract($mypath, $myupdate);
			\dash\code::exit();
		}
	}
}
?>