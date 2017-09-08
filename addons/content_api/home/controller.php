<?php
namespace addons\content_api\home;

class controller extends  \mvc\controller
{
	public function __construct()
	{
		\lib\storage::set_api(false);
		parent::__construct();
	}

	public function _route()
	{

		$url = \lib\router::get_url();
		if($url == '')
		{
			$this->redirector('api/v1')->redirect();
			return;
		}
	}
}
?>