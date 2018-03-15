<?php
namespace lib;
/**
 * this lib handle content of our PHP framework, Dash
 * v 0.1
 */
class content
{
	private static $name = 'content';
	private static $addr = root.'content';

	/**
	 * check specefic name for content is exist or not
	 * @param  [type] $_content_name [description]
	 * @return [type]                [description]
	 */
	public static function load($_content_name)
	{
		// list of addons exist in dash,
		$dash_addons = ['enter', 'su', 'cp', 'account', 'api'];
		$myrep       = 'content_'.$_content_name;

		// check content_aaa folder is exist in project or dash addons folder
		if(is_dir(root.$myrep))
		{
			return self::set($myrep);
		}
		// if exist on addons folder
		elseif(in_array($_content_name, $dash_addons) && is_dir(addons.$myrep))
		{
			return self::set($myrep, addons);
		}
		elseif($dynamic_sub_domain = self::dynamic_subdomain())
		{
			self::set($dynamic_sub_domain);
		}

		return null;
	}


	/**
	 * detect name of content folder
	 * @return [type] [description]
	 */
	public static function name()
	{
		$url_content = \lib\url::content();
		$content = 'content';
		if($url_content)
		{
			$content .= '_'. $url_content;
		}
		elseif($dynamic_sub_domain = self::dynamic_subdomain())
		{
			$content = $dynamic_sub_domain;
		}
		return $content;
	}


	public static function set($_name, $_addr = null)
	{
		// set name
		self::$name = $_name;
		// set addr of repository
		if($_addr)
		{
			self::$addr = $_addr. $_name;
		}
		else
		{
			self::$addr = root. $_name;
		}

		return self::$addr;
	}

	public static function get()
	{
		return self::$name;
	}

	public static function get_addr()
	{
		return self::$addr;
	}

	/**
	 * check for dynamic subdomain content exist or not
	 * @return [type] [description]
	 */
	private static function dynamic_subdomain()
	{
		if(\lib\url::subdomain())
		{
			// if we are in subdomain without finded repository
			// check if we have content_subDomain route in this folder
			if(is_dir(root. 'content_subdomain'))
			{
				return 'content_subdomain';
			}
			return false;
		}
		return null;
	}
}
?>
