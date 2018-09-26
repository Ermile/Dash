<?php
namespace content_su\dbtables;

class view
{
	public static function config()
	{
		\dash\data::page_title(T_("Database raw table data"));

		\dash\log::set('showDataTableRaw');

		// add back level to summary link
		$product_list_link        =  T_('Database raw table');
		\dash\data::page_desc(\dash\data::page_desc() . ' | '. $product_list_link);

		\dash\data::badge_link(\dash\url::this());
		\dash\data::badge_text(T_('Select table'));

		$search_string            = \dash\request::get('q');
		if($search_string)
		{
			\dash\data::page_title(\dash\data::page_title() . ' | '. T_('Search for :search', ['search' => $search_string]));
		}

		$table = \dash\request::get('table');
		if($table)
		{
			\dash\app\dbtables::$table = $table;

			$args =
			[
				'sort'  => \dash\request::get('sort'),
				'order' => \dash\request::get('order'),
			];

			\dash\data::allField(\dash\app\dbtables::get_field());

			\dash\data::sortLink(\content_su\view::su_make_sortLink(\dash\app\dbtables::sort_field(), \dash\url::here(). '/dbtables'));
			\dash\data::dataTable(\dash\app\dbtables::list(\dash\request::get('q'), $args));

			$check_empty_datatable = $args;
			unset($check_empty_datatable['sort']);
			unset($check_empty_datatable['order']);

			// set dataFilter
			\dash\data::dataFilter(\content_su\view::su_createFilterMsg($search_string, $check_empty_datatable));
		}
		else
		{
			$temp = \dash\db::get("Show tables");
			$show_all_tables = [];
			if(is_array($temp))
			{
				foreach ($temp as $key => $value)
				{
					$show_all_tables[current($value)] = T_(ucfirst(current($value)));
				}
			}
			\dash\data::allTables($show_all_tables);
		}

	}
}
?>