<?php
namespace addons\content_su\cronjob;

class model extends \addons\content_su\main\model
{
	public function post_cronjob()
	{
		if(\dash\request::post('active'))
		{
			\dash\engine\cronjob\options::active();
			\dash\notif::ok(T_("Your cronjob is actived"));
		}
		else
		{
			\dash\engine\cronjob\options::deactive();
			\dash\notif::warn(T_("Your cronjob is deactived"));
		}

		\dash\redirect::pwd();
	}
}
?>
