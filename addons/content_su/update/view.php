<?php
namespace content_su\update;

class view
{
	public static function config()
	{
		if(\dash\request::get('result') === 'true')
		{
			$result = \dash\session::get('lastUpdateGitResult');
			if($result && is_array($result))
			{
				foreach ($result as $key => $value)
				{
					echo $value;
				}
				\dash\code::boom();
			}
		}

		\dash\data::page_title(T_("Update"));
		\dash\data::page_desc(T_('Check curent version of dash and update to latest version if available.'));

		$dashLoc = null;
		// go to root url
		if(is_dir(root. 'dash'))
		{
			$dashLoc = T_('Inside project'). ' <span class="sf-chain-broken fc-green"></span>';
		}
		elseif(is_dir(root. '../dash'))
		{
			$dashLoc = T_('Global'). ' <span class="sf-globe-1 fc-red"></span>';
		}
		\dash\data::dashLoc($dashLoc);

		\dash\data::dash_projectVersion(\dash\utility\git::getLastUpdate(false));
		\dash\data::dash_projectCommitCount(\dash\utility\git::getCommitCount(false));
		\dash\data::dash_version(\dash\engine\version::get());
		\dash\data::dash_lastUpdate(\dash\utility\git::getLastUpdate());
		\dash\data::dash_commitCount(\dash\utility\git::getCommitCount());

		\dash\data::dash_dbVersion(\dash\db::db_version());
		\dash\data::dash_dbVersionDate(\dash\db::db_version(true, false, true));
		\dash\data::dash_dbVersionAddon(\dash\db::db_version(true, true));
		\dash\data::dash_dbVersionAddonDate(\dash\db::db_version(true, true, true));
	}
}
?>