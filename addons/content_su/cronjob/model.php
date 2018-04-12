<?php
namespace content_su\cronjob;

class model
{
	public static function post()
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
