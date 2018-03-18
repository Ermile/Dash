<?php
namespace addons\content_su\cronjob;

class model extends \addons\content_su\main\model
{
	public function post_cronjob()
	{
		if(\lib\request::post('active'))
		{
			\lib\utility\cronjob\options::active();
			\lib\notif::ok(T_("Your cronjob is actived"));
		}
		else
		{
			\lib\utility\cronjob\options::deactive();
			\lib\notif::warn(T_("Your cronjob is deactived"));
		}

		\lib\redirect::pwd();
	}
}
?>
