<?php
namespace content_support\ticket\contact_ticket;

class view
{
	public static function codeurl()
	{
		$codeurl = \dash\session::get('temp_ticket_codeurl');
		if($codeurl && !\dash\user::login())
		{
			\dash\data::tempTicketCodeURL($codeurl);
			\dash\session::clean('temp_ticket_codeurl');
		}
	}
}
?>