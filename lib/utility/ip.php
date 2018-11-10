<?php
namespace dash\utility;


class ip
{
	public static function check()
	{
		// if(!\dash\user::id())
		// {
		// 	return true;
		// }

		$ip = \dash\server::ip();

		if(self::is_local_ip($ip))
		{
			// return true;
		}

		if(self::is_block($ip))
		{
			\dash\header::status(423, T_("Your ip is blocked"));
		}

		if(self::is_not_block($ip))
		{
			return true;
		}

		self::new_ip($ip);
	}


	private static function files_addr()
	{
		$addr = root. 'public_html/files/ip/';
		if(!file_exists($addr))
		{
			\dash\file::makeDir($addr, null, true);
		}
		return $addr;
	}


	private static function is_block($_ip)
	{
		$addr = self::files_addr();
		$addr .= 'block';

		if(!is_file($addr))
		{
			return false;
		}

		if(\dash\file::search($addr, $_ip))
		{
			return true;
		}

		return false;

	}

	private static function is_not_block($_ip)
	{
		$addr = self::files_addr();
		$addr .= 'unblock';

		if(!is_file($addr))
		{
			return false;
		}

		if(\dash\file::search($addr, $_ip))
		{
			return true;
		}

		return false;
	}


	private static function new_ip($_ip)
	{
		$addr = self::files_addr();
		$addr .= 'new';

		if(!is_file($addr))
		{
			\dash\file::write($addr, $_ip. "\n");
			return false;
		}

		if(\dash\file::search($addr, $_ip))
		{
			return true;
		}

		\dash\file::append($addr, $_ip. "\n");
		return true;
	}


	private static function is_local_ip($_ip)
	{
		if(substr($_ip, 0, 8) === '127.0.0.')
		{
			return true;
		}
		return false;
	}
}
?>