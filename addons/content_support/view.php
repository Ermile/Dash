<?php
namespace content_support;

class view
{
	public static function config()
	{
		\dash\data::include_adminPanel(true);
		\dash\data::include_css(false);
		\dash\data::include_js(false);

		\dash\data::badge_shortkey(120);
		\dash\data::badge2_shortkey(121);

		\dash\data::include_chart(true);
		\dash\data::display_admin('content_support/layout.html');

	}


	public static function sidebarDetail($_all = false)
	{
		$args           = [];
		$args['type']   = 'ticket';
		$args['parent'] = null;

		if(!\dash\data::haveSubdomain())
		{
			if(\dash\data::subdomain())
			{
				$args['comments.subdomain']    = \dash\url::subdomain();
			}
			else
			{
				$args['comments.subdomain']    = null;
			}
		}

		$result               = [];

		if(\dash\data::accessMode() === 'mine')
		{
			$args['user_id'] = \dash\user::id();
			$result['all']   = $result['mine']  = \dash\db\comments::get_count(array_merge($args,[]));
		}
		else
		{
			$result['all']      = \dash\db\comments::get_count(array_merge($args, []));
		}

		$result['open']     = \dash\db\comments::get_count(array_merge($args,['status' => 'awaiting']));
		$result['archived'] = \dash\db\comments::get_count(array_merge($args,['status' => 'close']));

		if($_all)
		{
			unset($args['parent']);
			$result['message']    = \dash\db\comments::get_count($args);

			unset($args['status']);
			$args['parent']       = null;
			$result['avgfirst']   = \dash\db\comments::ticket_avg_first($args);
			$result['avgarchive'] = \dash\db\comments::ticket_avg_archive($args);
		}

		\dash\data::sidebarDetail($result);
		return $result;

	}
}
?>