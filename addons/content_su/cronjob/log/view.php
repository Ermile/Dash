<?php
namespace content_su\cronjob\log;


class view
{
	public static function config()
	{
		$data = \dash\file::read(core. '/lib/engine/cronjob/cronjob.me.log');

		\dash\data::cronjoblog($data);

	}
}
?>