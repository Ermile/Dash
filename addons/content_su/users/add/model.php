<?php
namespace addons\content_su\users\add;


class model extends \addons\content_su\main\model
{
	public function post_add($_args)
	{
		$request                = [];
		$request['mobile']      = \lib\request::post('mobile');
		$request['displayname'] = \lib\request::post('displayname');
		\lib\utility::set_request_array($request);
		$this->user_id = $this->login('id');
		$this->add_user();
	}
}
?>
