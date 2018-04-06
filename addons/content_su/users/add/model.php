<?php
namespace addons\content_su\users\add;


class model extends \addons\content_su\main\model
{
	public function post_add($_args)
	{
		$request                = [];
		$request['mobile']      = \dash\request::post('mobile');
		$request['displayname'] = \dash\request::post('displayname');
		\dash\utility::set_request_array($request);
		$this->user_id = \dash\user::id();
		$this->add_user();
	}
}
?>
