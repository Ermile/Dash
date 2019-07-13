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


	public static function can_edit_file($_name)
	{
		$return = false;
		switch ($_name)
		{
			case 'gitconfig':
				$return = true;
				break;

			case 'optionme':
				$return = false;
				break;

			default:
				$return = false;
				break;
		}
		return $return;
	}



	public static function get_addr_file($_name)
	{
		$addr = null;
		switch ($_name)
		{
			case 'gitconfig':
				\dash\log::set('nanoGitConfig');
				$addr = root. '.git/config';
				break;

			case 'optionme':
				\dash\log::set('nanoOptionMe');
				$addr = root. 'includes/option/option.me.php';
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