<?php
namespace addons\content_su\dbtables;

class view extends \addons\content_su\main\view
{
	public function config()
	{
		parent::config();

		$this->data->page['title'] = T_("Database raw table data");

		// add back level to summary link
		$product_list_link        =  T_('Database raw table');
		$this->data->page['desc'] .= ' | '. $product_list_link;


		$this->data->page['badge']['link'] = \lib\url::this(). '/dbtables';
		$this->data->page['badge']['text'] = T_('Select table');

		$search_string            = \lib\request::get('q');
		if($search_string)
		{
			$this->data->page['title'] .= ' | '. T_('Search for :search', ['search' => $search_string]);
		}

		$table = \lib\request::get('table');
		if($table)
		{
			\lib\app\dbtables::$table = $table;

			$args =
			[
				'sort'  => \lib\request::get('sort'),
				'order' => \lib\request::get('order'),
			];

			$this->data->all_field = \lib\app\dbtables::get_field();

			$this->data->sort_link = self::su_make_sort_link(\lib\app\dbtables::sort_field(), \lib\url::here(). '/dbtables');
			$this->data->dataTable = \lib\app\dbtables::list(\lib\request::get('q'), $args);

			$check_empty_datatable = $args;
			unset($check_empty_datatable['sort']);
			unset($check_empty_datatable['order']);

			// set dataFilter
			$this->data->dataFilter = $this->su_createFilterMsg($search_string, $check_empty_datatable);
		}
		else
		{
			$temp = \lib\db::get("Show tables");
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