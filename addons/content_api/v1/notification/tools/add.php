<?php
namespace content_api\v1\notification\tools;


trait add
{
	public function send_notification($_args = [])
	{
		$default_args =
		[
			'text'          => null,
			'cat'           => null,
			'to'            => null,
			'send_to_admin' => false,
		];

		if(!is_array($_args))
		{
			$_args = [];
		}

		$_args = array_merge($default_args, $_args);

		if(!$_args['text'])
		{
			return false;
		}

		if(!$_args['to'])
		{
			return false;
		}

		if(!$_args['cat'])
		{
			return false;
		}

		// save notification to send to user
		$notify_set =
        [
			'to'      => $_args['to'],
			'content' => $_args['text'],
			'cat'     => $_args['cat'],
        ];

        return \dash\db\notifications::set($notify_set);
	}
}
?>