<?php
namespace addons\content_cp\users\add;
use \lib\utility;
use \lib\debug;

class model extends \addons\content_cp\main\model
{
	public function post_add($_args)
	{
		$request                = [];
		$request['mobile']      = utility::post('mobile');
		$request['displayname'] = utility::post('displayname');
		utility::set_request_array($request);
		$this->user_id = $this->login('id');
		$this->add_user();
	}
}
?>
