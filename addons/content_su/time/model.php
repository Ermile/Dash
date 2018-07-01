<?php
namespace content_su\time;

class model
{
	public static function post()
	{


		if($resultBool)
		{
			\dash\notif::ok(implode(' ; ', $echo). " Sucessfully run!");
		}
		else
		{
			\dash\notif::error(implode(' ; ', $echo). " unsuccess!");
		}

	}


}
?>