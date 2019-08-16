<?php
namespace content_account\security\rememberme;


class view
{

	public static function config()
	{
		\dash\data::page_title(T_('Remember Me'));

		\dash\data::badge_link(\dash\url::this());
		\dash\data::badge_text(T_('Back to Account security'));

		\dash\data::isLtr(\dash\language::current('direction') === 'ltr' ? true : false);

		$id = \dash\user::id();

		if(!$id)
		{
			\dash\header::status(404, T_("Invalid user id"));
		}

		$user_detail = \dash\db\users::get_by_id($id);

		if(!$user_detail)
		{
			\dash\header::status(404, T_("User id not found"));
		}

		\dash\data::dataRow(\dash\app\user::ready($user_detail, true));

		self::session_list();
	}



	public static function session_list()
	{
		if(!\dash\user::login())
		{
			\dash\redirect::to(\dash\url::kingdom());
		}

		$user_id = \dash\user::id();
		$list    = \dash\db\sessions::get_active_sessions($user_id);

		if(!$list)
		{
			return false;
		}

		\dash\data::currentCookie(\dash\db\sessions::get_cookie());

		$mySessionData = [];
		foreach ($list as $key => $row)
		{
			@$mySessionData[$key]['id']         = $row['id'];
			@$mySessionData[$key]['code']       = $row['code'];
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

		// \dash\data::page_title(T_('Active sessions'));
		// \dash\data::page_desc(\dash\data::page_title());
	}
}
?>
