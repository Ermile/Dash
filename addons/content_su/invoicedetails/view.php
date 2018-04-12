<?php
namespace content_su\invoicedetails;

class view extends \addons\content_su\main\view
{
	public function view_list($_args)
	{
		$field = $this->controller()->fields;
		$list  = $this->model()->invoicedetails_list($_args, $field);
		$this->data->invoicedetails_list = $list;

		$this->orderUrl($_args, $field);
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