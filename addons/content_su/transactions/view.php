<?php
namespace addons\content_su\transactions;

class view extends \addons\content_su\main\view
{
	public function config()
	{
		parent::config();

		$this->data->page['title'] = T_("Transactions list");
		$this->data->page['desc']  = T_('Check list of Transactions and search or filter in them to find your transactions.'). ' '. T_('Also add or edit specefic transactions.');
		// add back level to summary link



		$this->data->page['badge']['link'] = \lib\url::this(). '/add';
		$this->data->page['badge']['text'] = T_('Add new transactions');

		$search_string            = \lib\request::get('q');
		if($search_string)
		{
			$this->data->page['title'] .= ' | '. T_('Search for :search', ['search' => $search_string]);
		}

		$args =
		[
			'sort'  => \lib\request::get('sort'),
			'order' => \lib\request::get('order'),
		];

		if(\lib\request::get('status'))
		{
			$args['transactions.status'] = \lib\request::get('status');
		}

		if(\lib\request::get('condition'))
		{
			$args['condition'] = \lib\request::get('condition');
		}

		if(\lib\request::get('payment'))
		{
			$args['payment'] = \lib\request::get('payment');
		}

		if(\lib\request::get('type'))
		{
			$args['transactions.type'] = \lib\request::get('type');
		}

		$this->data->sort_link  = self::su_make_sort_link(\lib\app\transaction::$sort_field, \lib\url::this());
		$this->data->dataTable = \lib\app\transaction::list(\lib\request::get('q'), $args);

		$check_empty_datatable = $args;
		unset($check_empty_datatable['sort']);
		unset($check_empty_datatable['order']);

		// set dataFilter
		$this->data->dataFilter = $this->su_createFilterMsg($search_string, $check_empty_datatable);
	}
}
?>