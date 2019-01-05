<?php
namespace content_hook\android\add;


class controller
{
	public static function routing()
	{
		\content_hook\android\verify::token();
	}


}
?>