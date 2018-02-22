<?php
namespace addons\content_cp\comments\edit;

class model extends \addons\content_cp\main\model
{
	public function post_change_status()
	{
		$status = \lib\utility::post('status');

		if(!$status)
		{
			\lib\debug::error(T_("Invalid status"));
			return false;
		}

		$post_detail = \lib\app\comment::edit(['status' => $status], \lib\utility::get('id'));

		if(\lib\debug::$status)
		{
			$this->redirector($this->url('full'));
		}
	}
}
?>
