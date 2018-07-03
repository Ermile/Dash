<?php
namespace content_su\nano;

class view
{
	public static function config()
	{
		\dash\data::page_title(T_("Nano"));
		\dash\data::page_desc(T_('Edit some server file'));

		$file = \dash\request::get('file');
		if($file)
		{
			$load = self::read_file($file);
			$addr = self::get_addr_file($file);
			\dash\data::readFile($load);
			\dash\data::readFileAddr($addr);
		}

	}

	public static function get_addr_file($_name)
	{
		$addr = null;
		switch ($_name)
		{
			case 'gitconfig':
				$addr = root. '.git/config';
				break;

			default:
				return false;
				break;
		}
		return $addr;

	}

	public static function read_file($_name)
	{
		$addr = self::get_addr_file($_name);

		if($addr && is_file($addr))
		{
			$load = \dash\file::read($addr);
			return $load;
		}
		else
		{
			\dash\header::status(501, "File not exitst");
		}
	}
}
?>