<?php
namespace content_su\ip;

class model
{
	public static function post()
	{
		$addr = root .'public_html/files/ip/'. \dash\request::post('file');

		if(\dash\request::post('type') === 'add' && \dash\request::post('file') && \dash\request::post('ip'))
		{
			$ip = \dash\request::post('ip');
			if(!filter_var($ip, FILTER_VALIDATE_IP))
			{
				\dash\notif::error(T_("Invalid ip syntax"));
				return false;
			}

			if(\dash\file::search($addr, $ip))
			{
				\dash\notif::warn(T_("This string is exist in file"));
				return false;
			}
			else
			{
				\dash\file::append($addr, $ip. "\n");
				\dash\notif::ok(T_("Ip added to this file"));
				\dash\redirect::pwd();
			}
		}
		if(\dash\request::post('type') === 'remove_ip' && \dash\request::post('file') && \dash\request::post('ip'))
		{
			$get = \dash\file::read($addr);
			$ip = \dash\request::post('ip');
			$get = str_replace($ip. "\n", '', $get);
			\dash\file::write($addr, $get);
			\dash\notif::ok(T_("Ip deleted"));
			\dash\redirect::pwd();

		}

	}
}
?>
