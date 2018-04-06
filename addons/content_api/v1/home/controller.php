<?php
namespace addons\content_api\v1\home;

class controller extends  \mvc\controller
{
	/**
	 * get storage api to show json in every view
	 */
	public function __construct()
	{
		$url = \dash\url::directory();
		if($url == 'v1')
		{
			\dash\temp::set('api', false);
		}
		else
		{
			\dash\temp::set('api', true);
		}

		parent::__construct();
	}


	public function ready()
	{
		$url = \dash\url::directory();

		if($url == 'v1')
		{
			\dash\redirect::to('v1/doc');
			return;
		}
	}


	/**
	 * method GET just allowed
	 */
	public function corridor()
	{
		if(!$this->method && $_SERVER['REQUEST_METHOD'] !== 'GET')
		{
			\dash\header::status(405, $_SERVER['REQUEST_METHOD'] . " not allowed");
		}
	}
}
?>