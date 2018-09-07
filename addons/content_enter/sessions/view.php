<?php
namespace content_enter\sessions;

class view
{

	public static function config()
	{
		if(!\dash\user::login())
		{
			\dash\redirect::to(\dash\url::kingdom());
		}

		$mySessions    = self::sessions_list();
		$mySessionData = [];
		foreach ($mySessions as $key => $row)
		{
			@$mySessionData[$key]['id']         = $row['id'];
			@$mySessionData[$key]['ip']         = long2ip($row['ip']);
			@$mySessionData[$key]['last']       = $row['last_seen'];
			@$mySessionData[$key]['browser']    = T_(ucfirst($row['agent_name']));
			@$mySessionData[$key]['browserVer'] = $row['agent_version'];
			@$mySessionData[$key]['os']         = $row['agent_os'];
			@$mySessionData[$key]['osVer']      = T_($row['agent_osnum']);

			if(isset($row['agent_os']))
			{
				switch ($row['agent_os'])
				{
					case 'nt':
						$mySessionData[$key]['os'] = T_('Windows');
						break;

					case 'lin':
						$mySessionData[$key]['os'] = T_('Linux');
						break;

					default:
						break;
				}
			}
		}

		\dash\data::sessionsList($mySessionData);

		\dash\data::page_title(T_('Active sessions'));
		\dash\data::page_desc(\dash\data::page_title());
	}


	public static function sessions_list()
	{
		if(\dash\user::login())
		{
			$user_id = \dash\user::id();
			$list    = \dash\db\sessions::get_active_sessions($user_id);
			return $list;
		}
		return [];
	}

}
?>