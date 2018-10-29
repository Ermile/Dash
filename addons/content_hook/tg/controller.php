<?php
namespace content_hook\tg;

class controller
{
	/**
	 * allow telegram to access to this location
	 * to send response to our server
	 * @return [type] [description]
	 */
	public static function routing()
	{
		$myhook = \dash\option::social('telegram', 'hookFolder');

		if(\dash\url::child() === $myhook)
		{
			// fire telegram api
			\dash\social\telegram\tg::fire();

			// bobooom :)
			\dash\code::boom();
		}

		// log access to this url
		\dash\log::set('tgUnauthorizedAccess');
		\dash\header::status(404);
	}
}
?>