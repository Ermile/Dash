<?php
namespace content_hook\gitdetail;


class view
{
	public static function config()
	{
		$dashLoc = null;
		// go to root url
		if(is_dir(root. 'dash'))
		{
			$dashLoc = 'inside';
		}
		elseif(is_dir(root. '../dash'))
		{
			$dashLoc = 'global';
		}

		$return                       = [];
		$return['site']               = \dash\url::site();
		$return['domain']             = \dash\url::domain();
		$return['name']               = \dash\data::site_title();
		$return['desc']               = \dash\data::site_desc();
		$return['logo']               = \dash\url::site(). '/static/images/logo.png';
		$return['dashLoc']            = $dashLoc;
		$return['projectVersion']     = \dash\utility\git::getLastUpdate(false);
		$return['projectCommitCount'] = \dash\utility\git::getCommitCount(false);
		$return['version']            = \dash\engine\version::get();
		$return['lastUpdate']         = \dash\utility\git::getLastUpdate();
		$return['commitCount']        = \dash\utility\git::getCommitCount();
		$return['dbVersion']          = \dash\db::db_version();
		$return['dbVersionDate']      = \dash\db::db_version(true, false, true);
		$return['dbVersionAddon']     = \dash\db::db_version(true, true);
		$return['dbVersionAddonDate'] = \dash\db::db_version(true, true, true);

		\dash\code::jsonBoom($return);
	}
}
?>