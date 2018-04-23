<?php
namespace content_su\update;

class view
{
	public static function config()
	{

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
	}
}
?>