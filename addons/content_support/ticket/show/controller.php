<?php
namespace content_support\ticket\show;

class controller
{
	public static function routing()
	{
		\dash\utility\ip::check(true);

		\dash\utility\hive::set();

		self::loadTicketDetail();
	}


	private static function loadTicketDetail()
	{
		$id = \dash\request::get('id');
		if(!$id)
		{
			return false;
		}

		$main = \dash\app\ticket::get($id);
		if(!$main || !array_key_exists('user_id', $main))
		{
			\dash\header::status(403, T_("Ticket not found"));
		}

		if(!\dash\permission::check('supportTicketManageSubdomain'))
		{
			if(!\dash\option::config('no_subdomain'))
			{
				$subdomain = \dash\url::subdomain();
				if(is_array($main) && array_key_exists('subdomain', $main) && $main['subdomain'] != $subdomain)
				{
					\dash\header::status(403, T_("Invalid Subdomain"));
				}
			}
		}

		\dash\data::loadTicketDetail($main);
		return $main;
	}
}
?>
