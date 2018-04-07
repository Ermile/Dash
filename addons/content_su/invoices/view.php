<?php
namespace content_su\invoices;

class view extends \addons\content_su\main\view
{
	public function view_list($_args)
	{

		$field = $this->controller()->fields;

		$list = $this->model()->invoices_list($_args, $field);

		$this->data->invoices_list = $list;

		$this->order_url($_args, $field);

		if(isset($this->controller->pagnation))
		{
			$this->data->pagnation = $this->controller->pagnation_get();
		}

		if(\dash\request::get('search'))
		{
			$this->data->get_search = \dash\request::get('search');
		}
	}

}
?>