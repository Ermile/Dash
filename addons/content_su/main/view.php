<?php
namespace addons\content_su\main;

class view extends \mvc\view
{
	public function config()
	{
		// $this->data->list             = $this->suModlueList('all');
		$this->data->bodyclass        = 'siftal';
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

		$this->data->display['su_posts'] = "content_su/posts/layout.html";
		$this->data->display['suSample'] = "content_su/sample/layout.html";

		$this->data->dash['langlist']    = ['fa_IR' => 'Persian - فارسی',
											 'en_US' => 'English',
											 'ar_SU' => 'Arabic - العربية'];

		$this->data->dir['right']        = $this->global->direction == 'rtl'? 'left':  'right';
		$this->data->dir['left']         = $this->global->direction == 'rtl'? 'right': 'left';
		$this->data->page['title']       = T_(ucfirst(\lib\router::get_url(' ')));

		$this->data->dash['version']     = \lib\engine::getLastVersion();
		$this->data->dash['lastUpdate']  = \lib\engine::getLastUpdate();
	}


	public function view_child()
	{
		$mytable                = $this->suModule('table');
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




	public static function su_make_sort_link($_field, $_url)
	{
		$get = \lib\utility::get(null, 'raw');
		if(!is_array($get))
		{
			$get = [];
		}

		$default_get =
		[
			'q'     => null,
			'sort'  => null,
			'order' => null,
		];

		$get          = array_merge($default_get, $get);
		$get['order'] = mb_strtolower($get['order']);
		$get['sort']  = mb_strtolower($get['sort']);

		$link = [];

		foreach ($_field as $key => $field)
		{
			$temp_link         = [];
			$temp_link['sort'] = $field;

			if($field === $get['sort'])
			{
				$temp_link['order'] = 'asc';
				if($get['order'] === 'asc')
				{
					$temp_link['order'] = 'desc';
				}
				$link[$field]['order'] = $temp_link['order'] === 'asc' ? 'desc' : 'asc';
			}
			else
			{
				$temp_link['order']    = 'asc';
				$link[$field]['order'] = null;
			}

			$temp_link['q']    = $get['q'];

			if(is_array(\lib\utility::get(null , 'raw')))
			{
				foreach (\lib\utility::get(null , 'raw') as $query_key => $query_value)
				{
					if(!in_array($query_key, ['q', 'sort', 'order']))
					{
						$temp_link[$query_key] = $query_value;
					}
				}
			}

			$link[$field]['link'] = $_url . '?'.  http_build_query($temp_link);
		}
		return $link;
	}


	public function su_createFilterMsg($_searchText, $_filterArray)
	{
		$result = null;

		if($_searchText)
		{
			$result = T_('Search with keyword :search', ['search' => '<b>'. $_searchText. '</b>']);
		}

		if($_filterArray)
		{
			$result .= ' '. T_('with condition'). ' ';
			$index  = 0;
			foreach ($_filterArray as $key => $value)
			{
				if($result && $index > 0)
				{
					$result .= T_(', ');
				}
				if($value === 1)
				{
					$value = 'enable';
				}
				elseif($value === 0)
				{
					$value = 'disable';
				}
				if(is_numeric($value))
				{
					$value = \lib\utility\human::fitNumber($value);
				}
				$result .= T_($key) . ' <b>'. T_(ucfirst($value)). '</b>';
				$index++;
			}
		}

		return $result;
	}
}
?>