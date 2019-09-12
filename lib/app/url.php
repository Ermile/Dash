<?php
namespace dash\app;

class url
{

	public static function check($_url, $_option = [])
	{
		$raw_url = str_replace('/', '', $_url);
		if(mb_strlen($raw_url) === 1 || mb_strlen($_url) === 1)
		{
			\dash\notif::error(T_("You cannot select one or two character addresses"), ['element' => ['url', 'slug', 'title']]);
			return false;
		}

		if(mb_strlen($raw_url) === 2 || mb_strlen($_url) === 2)
		{
			\dash\notif::error(T_("You cannot select one or two character addresses"), ['element' => ['url', 'slug', 'title']]);
			return false;
		}

		$disallow =
		[
			'cat', 'tag', 'term', 'file', 'files', 'static', 'sitemap', 'index',
		];

		$disallow = array_merge($disallow, \dash\engine\content::content_list());

		$option = \dash\option::config('disallow_url');
		if($option && is_array($option))
		{
			$disallow = array_merge($option, $disallow);
		}

		if(in_array($raw_url, $disallow) || in_array($_url, $disallow))
		{
			\dash\notif::error(T_("This address is a system keyword and cannot be selected"), ['element' => ['url', 'slug', 'title']]);
			return false;
		}

		return true;
	}
}
?>