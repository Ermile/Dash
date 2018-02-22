<?php
namespace addons\content_su\dbtables;

class view extends \addons\content_su\main\view
{
	public function config()
	{
		parent::config();
		$this->data->modulePath = $this->url('baseFull');

		$this->data->page['title'] = T_("Database raw table data");

		// add back level to summary link
		$product_list_link        =  T_('Database raw table');
		$this->data->page['desc'] .= ' | '. $product_list_link;


		$this->data->page['badge']['link'] = $this->data->modulePath. '/dbtables';
		$this->data->page['badge']['text'] = T_('Select table');

		$search_string            = \lib\utility::get('q');
		if($search_string)
		{
			$this->data->page['title'] .= ' | '. T_('Search for :search', ['search' => $search_string]);
		}

		$table = \lib\utility::get('table');
		if($table)
		{
			\lib\app\dbtables::$table = $table;

			$args =
			[
				'sort'  => \lib\utility::get('sort'),
				'order' => \lib\utility::get('order'),
			];

			$this->data->all_field = \lib\app\dbtables::get_field();

			$this->data->sort_link = self::make_sort_link(\lib\app\dbtables::sort_field(), $this->url('baseFull'). '/dbtables');
			$this->data->dataTable = \lib\app\dbtables::list(\lib\utility::get('q'), $args);

			$check_empty_datatable = $args;
			unset($check_empty_datatable['sort']);
			unset($check_empty_datatable['order']);

			// set dataFilter
			$this->data->dataFilter = $this->createFilterMsg($search_string, $check_empty_datatable);
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