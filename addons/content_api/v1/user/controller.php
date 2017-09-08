<?php
namespace addons\content_api\v1\user;

class controller extends  \addons\content_api\v1\home\controller
{

	public function _route()
	{
		/**
		 * link to upload
		 */
		$this->link("upload")->ALL("v1/user");

		/**
		 * post user to upload
		 */
		$this->post("upload")->ALL("v1/user");

		/**
		 * get to load upload details
		 */
		$this->get("upload")->ALL("v1/user");

	}
}
?>