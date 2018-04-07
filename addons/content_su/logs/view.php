<?php
namespace content_su\logs;

class view extends \addons\content_su\main\view
{
	public function config()
	{
		parent::config();
		$this->data->page['title'] = T_("Logs list");
		$this->data->page['desc'] = T_("All event in this system");
	}


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

		if(\dash\request::get('search'))
		{
			$this->data->get_search = \dash\request::get('search');
		}
	}
}
?>