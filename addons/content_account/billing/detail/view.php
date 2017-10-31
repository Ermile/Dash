<?php
namespace content_account\billing\detail;

class view extends \content_account\main\view
{
	public function config()
	{
		$this->data->page['title'] = T_("Billing usage detail");
		$this->data->page['desc']  = T_("Check your current usage and active user and price for this period of time.");
	}


	/**
	 * { function_description }
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function view_detail($_args)
	{
		if(!$this->login())
		{
			return;
		}
		// var_dump($_args);exit();
		$detail = $_args->api_callback;
		$this->data->detail = $detail;

	}

}
?>