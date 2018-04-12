<?php
namespace content_su\users;

class view extends \addons\content_su\main\view
{
	public function view_list($_args)
	{

		$field = $this->controller()->fields;

		$list = $this->model()->users_list($_args, $field);

		$this->data->users_list = $list;

		$this->orderUrl($_args, $field);

		if(isset($this->controller->pagnation))
		{
			$this->data->pagnation = $this->controller->pagnation_get();
		}

		if(isset($_args->get("search")[0]))
		{
			$this->data->get_search = $_args->get("search")[0];
		}
	}


	/**
	 * MAKE ORDER URL
	 *
	 * @param      <type>  $_args    The arguments
	 * @param      <type>  $_fields  The fields
	 */
	public function orderUrl($_args, $_fields)
	{
		$orderUrl = [];
		foreach ($_fields as $key => $value)
		{

			if(isset($_args->get("sort")[0]))
			{
				if($_args->get("sort")[0] == $value)
				{
					if(mb_strtolower($_args->get("order")[0]) == mb_strtolower('ASC'))
					{
						$orderUrl[$value] = "sort=$value/order=desc";
					}
					else
					{
						$orderUrl[$value] = "sort=$value/order=asc";
					}
				}
				else
				{

					$orderUrl[$value] = "sort=$value/order=asc";
				}
			}
			else
			{
				$orderUrl[$value] = "sort=$value/order=asc";
			}
		}

		$this->data->orderUrl = $orderUrl;
	}
}
?>