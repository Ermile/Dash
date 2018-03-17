<?php
namespace addons\content_cp\comments\edit;

class model extends \addons\content_cp\main\model
{
	public function post_change_status()
	{
		$status = \lib\request::post('status');

		if(!$status)
		{
			\lib\notif::error(T_("Invalid status"));
			return false;
		}

		$post_detail = \lib\app\comment::edit(['status' => $status], \lib\request::get('id'));

		if(\lib\notif::$status)
		{
			\lib\redirect::pwd();
		}
	}
}
?>
