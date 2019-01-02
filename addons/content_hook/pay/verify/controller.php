<?php
namespace content_hook\pay\verify;


class controller
{
	public static function routing()
	{
		$bank = \dash\url::subchild();
		if($bank)
		{
			$args            = [];
			$args['get']     = \dash\request::get();
			$args['post']    = \dash\request::post();
			$args['request'] = \dash\safe::safe($_REQUEST);

			\dash\utility\pay\verify::verify($bank, $args);
		}
	}
}
?>