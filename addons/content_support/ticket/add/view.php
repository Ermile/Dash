<?php
namespace content_support\ticket\add;

class view
{

	public static function config()
	{
		\dash\data::page_title(T_("Add new ticket"));
		\dash\data::page_desc(T_("Dot worry!"). ' '. T_("Ask your question."). ' '. T_("We are here to answer your questions."));
	}
}
?>