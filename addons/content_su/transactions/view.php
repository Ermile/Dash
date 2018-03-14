<?php
namespace addons\content_su\transactions;

class view extends \addons\content_su\main\view
{
	public function config()
	{
		parent::config();
		$this->data->modulePath = \lib\url::here() . '/transactions';

		$this->data->page['title'] = T_("Transactions list");
		$this->data->page['desc']  = T_('Check list of Transactions and search or filter in them to find your transactions.'). ' '. T_('Also add or edit specefic transactions.');
		// add back level to summary link



		$this->data->page['badge']['link'] = $this->data->modulePath. '/add';
		$this->data->page['badge']['text'] = T_('Add new transactions');

		$search_string            = \lib\utility::get('q');
		if($search_string)
		{
			$this->data->page['title'] .= ' | '. T_('Search for :search', ['search' => $search_string]);
		}

		$args =
		[
			'sort'  => \lib\utility::get('sort'),
			'order' => \lib\utility::get('order'),
		];

		if(\lib\utility::get('status'))
		{
			$args['transactions.status'] = \lib\utility::get('status');
		}

		if(\lib\utility::get('condition'))
		{
			$args['condition'] = \lib\utility::get('condition');
		}

		if(\lib\utility::get('payment'))
		{
			$args['payment'] = \lib\utility::get('payment');
		}

		if(\lib\utility::get('type'))
		{
			$args['transactions.type'] = \lib\utility::get('type');
		}

		$this->data->sort_link  = self::su_make_sort_link(\lib\app\transaction::$sort_field, $this->data->modulePath);
		$this->data->dataTable = \lib\app\transaction::list(\lib\utility::get('q'), $args);

		$check_empty_datatable = $args;
		unset($check_empty_datatable['sort']);
		unset($check_empty_datatable['order']);

		// set dataFilter
		$this->data->dataFilter = $this->su_createFilterMsg($search_string, $check_empty_datatable);
	}
}
?>