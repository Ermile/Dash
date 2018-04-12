<?php
namespace content_su\cronjob;


class view
{
	public static function config()
	{
		\dash\data::cronjob(\dash\engine\cronjob\options::status());
	}
}
?>