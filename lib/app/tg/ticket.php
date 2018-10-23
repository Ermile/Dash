<?php
namespace dash\app\tg;


class ticket
{
	public static function answer($_id, $_answer)
	{
		// save answer
		\content_support\ticket\show\model::answer_save($_id, $_answer);
		return true;
	}


	public static function create($_title, $_content)
	{
		\content_support\ticket\add\model::add_new($_title, $_content);
	}
}
?>