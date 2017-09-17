<?php
namespace addons\content_su\main;

class view extends \mvc\view
{
	public function config()
	{
		// $this->data->list             = $this->suModlueList('all');
		$this->data->bodyclass        = 'fixed unselectable siftal';
		$this->include->css           = false;
		$this->include->js            = false;
		// $this->include->fontawesome   = true;
		// $this->include->datatable     = true;
		// $this->include->chart         = true;
		// $this->include->introjs       = true;
		// $this->include->lightbox      = true;
		// $this->include->editor        = true;
		// $this->include->su            = true;
		// $this->include->uploader      = true;
		$this->global->js             = [];

		$this->data->display['su_posts']  = "content_su/posts/layout.html";
		$this->data->display['suSample'] = "content_su/sample/layout.html";


		$this->data->dash['version']    = \lib\dash::getLastVersion();
		$this->data->dash['lastUpdate'] = \lib\dash::getLastUpdate();
		$this->data->dash['langlist']   = ['fa_IR' => 'Persian - فارسی',
											 'en_US' => 'English',
											 'ar_SU' => 'Arabic - العربية'];

		// $this->global->js             = [$this->url->myStatic.'js/highcharts/highcharts.js'];
		// $this->data->page['desc']  = 'salam';
		$mymodule = $this->module();

		$this->data->page['title']    = T_(ucfirst(\lib\router::get_url(' ')));


		// $this->data->suModule         = $this->suModule();

		$this->data->dir['right']     = $this->global->direction == 'rtl'? 'left':  'right';
		$this->data->dir['left']      = $this->global->direction == 'rtl'? 'right': 'left';
	}


	public function view_child()
	{
		$mytable                = $this->suModule('table');
		$mychild                = $this->child();
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