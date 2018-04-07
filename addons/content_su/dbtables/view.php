<?php
namespace content_su\dbtables;

class view extends \addons\content_su\main\view
{
	public function config()
	{
		parent::config();

		$this->data->page['title'] = T_("Database raw table data");

		// add back level to summary link
		$product_list_link        =  T_('Database raw table');
		$this->data->page['desc'] .= ' | '. $product_list_link;


		$this->data->page['badge']['link'] = \dash\url::this(). '/dbtables';
		$this->data->page['badge']['text'] = T_('Select table');

		$search_string            = \dash\request::get('q');
		if($search_string)
		{
			$this->data->page['title'] .= ' | '. T_('Search for :search', ['search' => $search_string]);
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

			$this->data->all_field = \dash\app\dbtables::get_field();

			$this->data->sort_link = self::su_make_sort_link(\dash\app\dbtables::sort_field(), \dash\url::here(). '/dbtables');
			$this->data->dataTable = \dash\app\dbtables::list(\dash\request::get('q'), $args);

			$check_empty_datatable = $args;
			unset($check_empty_datatable['sort']);
			unset($check_empty_datatable['order']);

			// set dataFilter
			$this->data->dataFilter = $this->su_createFilterMsg($search_string, $check_empty_datatable);
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
			$this->data->all_tables = $show_all_tables;
		}

	}
}
?>