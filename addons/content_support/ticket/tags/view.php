<?php
namespace content_support\ticket\tags;

class view
{

	public static function config()
	{


		$args =
		[
			'order' => \dash\request::get('order'),
			'sort'  => \dash\request::get('sort'),
		];

		if(!$args['order'])
		{
			$args['order'] = 'DESC';
		}


		$args['language'] = \dash\language::current();
		$args['type'] = 'support_tag';

		if(isset($args['type']) && $args['type'] === 'cat')
		{
			if(!\dash\permission::check('cpCategoryView'))
			{
				// @check
			}
		}

		$search_string = \dash\request::get('q');

		if($search_string)
		{
			$myTitle = T_('Search'). ' '.  $search_string;
		}


		$dataTable = \dash\app\term::list($search_string, $args);
		\dash\data::sortLink(\content_cms\view::make_sort_link(\dash\app\term::$sort_field, \dash\url::this()));
		\dash\data::dataTable($dataTable);

		if(\dash\request::get('edit'))
		{
			\dash\data::editMode(true);

			$id = \dash\request::get('edit');
			$datarow = \dash\app\term::get($id);
			\dash\data::datarow($datarow);

			if(!$datarow)
			{
				\dash\header::status(404, T_("Id not found"));
			}
		}
	}

}
?>