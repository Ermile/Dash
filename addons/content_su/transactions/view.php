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



		$this->data->page['badge']['link'] = \dash\url::this(). '/add';
		$this->data->page['badge']['text'] = T_('Add new transactions');

		$search_string            = \dash\request::get('q');
		if($search_string)
		{
			$this->data->page['title'] .= ' | '. T_('Search for :search', ['search' => $search_string]);
		}

		$args =
		[
			'sort'  => \dash\request::get('sort'),
			'order' => \dash\request::get('order'),
		];

		if(\dash\request::get('status'))
		{
			$args['transactions.status'] = \dash\request::get('status');
		}

		if(\dash\request::get('condition'))
		{
			$args['condition'] = \dash\request::get('condition');
		}

		if(\dash\request::get('payment'))
		{
			$args['payment'] = \dash\request::get('payment');
		}

		if(\dash\request::get('type'))
		{
			$args['transactions.type'] = \dash\request::get('type');
		}

		$this->data->sort_link  = self::su_make_sort_link(\dash\app\transaction::$sort_field, \dash\url::this());
		$this->data->dataTable = \dash\app\transaction::list(\dash\request::get('q'), $args);

		$check_empty_datatable = $args;
		unset($check_empty_datatable['sort']);
		unset($check_empty_datatable['order']);

		// set dataFilter
		$this->data->dataFilter = $this->su_createFilterMsg($search_string, $check_empty_datatable);
	}
}
?>