<?php
namespace content_su\logcaller;


class view
{
	public static function config()
	{
		\dash\data::logcoller(\dash\log::lists());
	}
}
?>