<?php
namespace content_support\ticket\show;

class view
{

	public static function config()
	{
		\dash\data::page_title(T_("Add new ticket"));
		\dash\data::page_desc(T_("Dot worry!"). ' '. T_("Ask your question."). ' '. T_("We are here to answer your questions."));

		\dash\data::page_pictogram('comments');

		$parent = \dash\request::get('id');
		$parent = \dash\coding::decode($parent);

		if(!$parent)
		{
			\dash\header::status(404, T_("Invalid id"));
		}

		$args['sort']    = 'id';
		$args['order']   = 'desc';
		$args['type']    = 'ticket';
		$args['user_id'] = \dash\user::id();
		$args['parent']  = $parent;

		$dataTable = \dash\app\comment::list(null, $args);

		$main = \dash\app\comment::get(\dash\request::get('id'));
		array_push($dataTable, $main);

		\dash\data::dataTable($dataTable);
	}
}
?>