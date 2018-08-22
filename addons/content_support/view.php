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

		if(\dash\permission::check('supportTicketView'))
		{
			\dash\data::sidebarDetail(self::sidebarDetail());
		}
	}


	private static function sidebarDetail()
	{
		if(\dash\url::subdomain())
		{
			$args['comments.subdomain']    = \dash\url::subdomain();
		}
		else
		{
			$args['comments.subdomain']    = null;
		}

		$result               = [];
		$result['unanswered'] = \dash\db\comments::get_count(array_merge($args,['type' => 'ticket', 'answertime' => null]));
		$result['all']        = \dash\db\comments::get_count(array_merge($args, ['type' => 'ticket']));
		$result['mine']       = \dash\db\comments::ticket_mine(\dash\user::id());

		return $result;

	}
}
?>