<?php
namespace addons\content_su\logs;

class view extends \addons\content_su\main\view
{
	public function view_list($_args)
	{

		$field                 = $this->controller()->fields;

		$list                  = $this->model()->logs_list($_args, $field);
		$this->data->logs_list = $list;

		$this->order_url($_args, $field);

		if(isset($this->controller->pagnation))
		{
			$this->data->pagnation = $this->controller->pagnation_get();
		}

		if(\lib\utility::get('search'))
		{
			$this->data->get_search = \lib\utility::get('search');
		}
	}
}
?>