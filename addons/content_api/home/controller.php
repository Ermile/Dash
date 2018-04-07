<?php
namespace content_api\home;

class controller extends  \mvc\controller
{
	public function __construct()
	{
		\dash\temp::set('api', false);
		parent::__construct();
	}

	public function ready()
	{

		$url = \dash\url::directory();
		if($url == '')
		{
			\dash\redirect::to('api/v1');
			return;
		}
	}
}
?>