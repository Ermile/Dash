<?php
namespace dash\utility;

/**
* CHECK IP IS BLOCKE
*/
class ip
{

	public static function check($_if_login_is_ok = false)
	{
		if($_if_login_is_ok)
		{
			if(\dash\user::id())
			{
				return true;
			}
		}

		$ip = \dash\server::ip();

		if(self::is_local_ip($ip))
		{
			return true;
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

	// run this function from cronjob
	public static function check_is_block()
	{
		$addr = self::files_addr();
		$addr .= 'new';

		if(!is_file($addr))
		{
			return true;
		}

		$get = \dash\file::read($addr);
		if(!$get)
		{
			return true;
		}

		$get = explode("\n", $get);
		$get = array_filter($get);
		$get = array_unique($get);

		// wrire file to not check exist file in foreach
		$addr_result = self::files_addr();
		$addr_result .= 'apiresult';

		if(!is_file($addr_result))
		{
			\dash\file::write($addr_result, '');
		}

		$is_block     = [];
		$is_not_block = [];

		foreach ($get as $key => $value)
		{
			if(!$value)
			{
				continue;
			}

			$check = self::get_from_server($value);

			if(isset($check[0]))
			{
				if($check[0] == 'Y')
				{
					$is_block[] = $value;
				}
				elseif($check[0] == 'N')
				{
					$is_not_block[] = $value;
				}
			}
		}

		// get new file again
		$addr = self::files_addr();

		$get = \dash\file::read($addr. 'new');

		if(!empty($is_block))
		{
			foreach ($is_block as $key => $value)
			{
				$get = str_replace($value. "\n", "", $get);
				\dash\file::append($addr. 'block', $value. "\n");
			}
		}

		if(!empty($is_not_block))
		{
			foreach ($is_not_block as $key => $value)
			{
				$get = str_replace($value. "\n", "", $get);
				\dash\file::append($addr. 'unblock', $value. "\n");
			}
		}

		\dash\file::write($addr. 'new', $get);

		return true;
	}


	private static function get_from_server($_ip)
	{
		// save result to file
		$addr_result = self::files_addr();
		$addr_result .= 'apiresult';

		$search = \dash\file::search($addr_result, $_ip);
		if($search && is_array($search))
		{
			foreach ($search as $key => $value)
			{
				$result = explode("=", $value);

				if(isset($result[1]))
				{
					$explode = explode('|', $result[1]);
					return $explode;
				}
			}
		}

		$apiKey = \dash\option::config('botscout');

		if(!$apiKey)
		{
			return false;
		}

		$url    = "http://botscout.com/test/?ip=$_ip&key=$apiKey";
		$data   = file_get_contents($url);

		\dash\file::append($addr_result, $_ip. "=". $data. "\n");

		$explode = explode('|', $data);

		// sample 'MULTI' return string (standard API, not XML)
		// Y|MULTI|IP|4|MAIL|26|NAME|30

		// $explode[0] - 'Y' if found in database, 'N' if not found, '!' if an error occurred
		// $explode[1] - type of test (will be 'MAIL', 'IP', 'NAME', or 'MULTI')
		// $explode[2] - descriptor field for item (IP)
		// $explode[3] - how many times the IP was found in the database
		// $explode[4] - descriptor field for item (MAIL)
		// $explode[5] - how many times the EMAIL was found in the database
		// $explode[6] - descriptor field for item (NAME)
		// $explode[7] - how many times the NAME was found in the database

		return $explode;


	}
}
?>