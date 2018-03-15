<?php
namespace addons\content_su\notifications;

class view extends \addons\content_su\main\view
{
	public function view_list($_args)
	{

		$field = $this->controller()->fields;

		$list                           = $this->model()->notifications_list($_args, $field);

		$this->data->notifications_list = $list;
		$this->order_url($_args, $field);
		if(isset($this->controller->pagnation))
		{
			$this->data->pagnation = $this->controller->pagnation_get();
		}

		if(\lib\request::get('search'))
		{
			$this->data->get_search = \lib\request::get('search');
		}
	}

}
?>