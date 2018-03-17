<?php
namespace addons\content_api\home;

class controller extends  \mvc\controller
{
	public function __construct()
	{
		\lib\temp::set('api', false);
		parent::__construct();
	}

	public function ready()
	{

		$url = \lib\url::directory();
		if($url == '')
		{
			\lib\redirect::to('api/v1');
			return;
		}
	}
}
?>