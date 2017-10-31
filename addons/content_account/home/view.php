<?php
namespace content_account\home;

class view extends \content_account\main\view
{
	/**
	 * title and desc of page
	 */
	public function config()
	{
		$this->data->page['title'] = T_("Dashboard");
		$this->data->page['desc'] = T_("View account summary");
	}


	/**
	 * view all team and branch
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function view_dashboard($_args)
	{

	}
}
?>