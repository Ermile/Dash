<?php
namespace content_su\cronjob;

class model
{
	public static function post()
	{
		$post = \dash\request::post();

		\dash\engine\cronjob\options::save_list($post);

		if(\dash\request::post('active'))
		{
			\dash\log::set('cronJobChange');
			\dash\engine\cronjob\options::active();
			\dash\notif::ok(T_("Your cronjob is actived"));
		}
		else
		{
			\dash\log::set('cronJobDeactive');
			\dash\engine\cronjob\options::deactive();
			\dash\notif::warn(T_("Your cronjob is deactived"));
		}

		\dash\redirect::pwd();
	}
}
?>
