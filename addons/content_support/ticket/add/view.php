<?php
namespace content_support\ticket\add;

class view
{

	public static function config()
	{
		\dash\data::page_title(T_("Add new ticket"));
		\dash\data::page_desc(T_("Dot worry!"). ' '. T_("Ask your question."). ' '. T_("We are here to answer your questions."));
		\dash\data::page_pictogram('plus-circle');
		\dash\data::badge_text(T_('Back to tickets list'));
		\dash\data::badge_link(\dash\url::this(). \dash\data::accessGet());

		\dash\session::set('ticket_load_page_time', time());
	}
}
?>