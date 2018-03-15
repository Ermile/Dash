<?php
namespace lib;
/**
 * this lib handle content of our PHP framework, Dash
 * v 0.1
 */
class content
{
	/**
	 * check specefic name for content is exist or not
	 * @param  [type] $_content_name [description]
	 * @return [type]                [description]
	 */
	public static function check($_content_name)
	{
		// list of addons exist in dash,
		$dash_addons = ['cp', 'enter', 'api', 'su', 'account'];

		// set repository name
		$myrep = 'content_'. $_content_name;

		$is_content = null;

		// check content_aaa folder is exist in project or dash addons folder
		if(is_dir(root.$myrep))
		{
			$is_content = true;
		}
		// if exist on addons folder
		elseif(in_array($_content_name, $dash_addons) && is_dir(addons.$myrep))
		{
			$is_content = true;
		}

		return $is_content;
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
			$content .= '_'. $dynamic_sub_domain;
		}
		return $content;
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
				return 'subdomain';
			}
			return false;
		}
		return null;
	}
}
?>