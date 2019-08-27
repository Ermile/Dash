<?php
namespace content_su\cronjob;


class view
{
	public static function config()
	{
		\dash\data::cronjob(\dash\engine\cronjob\options::status());
		\dash\data::cronjobPHP(\dash\engine\cronjob\options::current_cronjob_path());
		$list = \dash\engine\cronjob\options::list();
		\dash\data::activeList($list);

		$unixcrontab = \dash\engine\cronjob\options::unixcrontab();
		\dash\data::unixcrontab($unixcrontab);

		$masterurl = \dash\engine\cronjob\options::masterurl();
		\dash\data::masterurl($masterurl);


	}
}
?>