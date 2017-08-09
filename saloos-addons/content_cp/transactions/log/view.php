<?php
namespace addons\content_cp\transactions\log;

class view extends \mvc\view
{
	public function view_log($_args)
	{

		$field =
		[
			'id',
			'title',
			'transactionitem_id',
			'user_id',
			'type',
			'unit',
			'plus',
			'minus',
			'budgetbefore',
			'budget',
			'status',
			'meta',
			'desc',
			'related_user_id',
			'parent_id',
			'finished',
			'date',
			'mobile',
			'displayname',
			'caller',
		];

		$list = $this->model()->transactions_log_list($_args, $field);

		$this->data->transactions_log_list = $list;

		$this->order_url($_args, $field);

		if(isset($this->controller->pagnation))
		{
			$this->data->pagnation = $this->controller->pagnation_get();
		}

		if(\lib\utility::get('search'))
		{
			$url = $this->url('full');
			$url = preg_replace("/search\=(.*)(\/|)/", "search=". \lib\utility::get('search'), $url);
			$this->redirector($url)->redirect();
		}

		if(isset($_args->get("search")[0]))
		{
			$this->data->get_search = $_args->get("search")[0];
		}

		if(\lib\utility::get('mobile'))
		{
			$this->data->get_mobile = \lib\utility\filter::mobile(\lib\utility::get('mobile'));
		}
	}


	/**
	 * MAKE ORDER URL
	 *
	 * @param      <type>  $_args    The arguments
	 * @param      <type>  $_fields  The fields
	 */
	public function order_url($_args, $_fields)
	{
		$order_url = [];
		foreach ($_fields as $key => $value)
		{

			if(isset($_args->get("sort")[0]))
			{
				if($_args->get("sort")[0] == $value)
				{
					if(mb_strtolower($_args->get("order")[0]) == mb_strtolower('ASC'))
					{
						$order_url[$value] = "sort=$value/order=desc";
					}
					else
					{
						$order_url[$value] = "sort=$value/order=asc";
					}
				}
				else
				{

					$order_url[$value] = "sort=$value/order=asc";
				}
			}
			else
			{
				$order_url[$value] = "sort=$value/order=asc";
			}
		}

		$this->data->order_url = $order_url;
	}
}
?>