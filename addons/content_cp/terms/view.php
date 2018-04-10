<?php
namespace content_cp\terms;


class view
{
	public static function config()
	{

		$myTitle = T_("Terms");
		$myDesc  = T_("Check terms and filter by type or view and edit some terms");

		$myType = \dash\request::get('type');
		if($myType)
		{
			switch ($myType)
			{
				case 'cat':
				case 'category':
					$myTitle = T_('Categories');
					$myDesc  = T_("Check categories and add or edit some new category");
					break;

				case 'tag':
					$myTitle = T_('Tags');
					$myDesc  = T_("Check tags and add or edit some new tag");
					break;
			}
		}

		\dash\data::page_title($myTitle);
		\dash\data::page_desc($myDesc);

		\dash\data::badge_text(T_('Back to dashboard'));
		\dash\data::badge_link(\dash\url::here());



		$args =
		[
			'order' => \dash\request::get('order'),
			'sort'  => \dash\request::get('sort'),
		];

		if(!$args['order'])
		{
			$args['order'] = 'DESC';
		}

		if($myType)
		{
			if($myType === 'category')
			{
				$args['type'] = 'cat';
			}
			else
			{
				$args['type'] = $myType;
			}
		}

		$search_string = \dash\request::get('q');

		if($search_string)
		{
			$myTitle = T_('Search'). ' '.  $search_string;
		}

		$export = false;
		if(\dash\request::get('export') === 'true')
		{
			$export = true;
			$args['pagenation'] = false;
		}

		$dataTable = \dash\app\term::list($search_string, $args);
		\dash\data::dataTable($dataTable);


		if($export)
		{
			\dash\utility\export::csv(['name' => 'export_service', 'data' => $dataTable]);
		}

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
