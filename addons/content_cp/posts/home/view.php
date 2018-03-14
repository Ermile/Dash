<?php
namespace addons\content_cp\posts\home;


class view extends \addons\content_cp\posts\main\view
{
	public function config()
	{
		parent::config();

		$myType = \lib\utility::get('type');

		$this->data->page['title'] = T_("Posts");
		$this->data->page['desc']  = T_('Check list of posts and search or filter in them to find your posts.'). ' '. T_('Also add or edit specefic post.');

		$this->data->page['badge']['link'] = $this->data->modulePath. '/add'. $this->data->moduleType;
		$this->data->page['badge']['text'] = T_('Add new :val', ['val' => $myType]);


		if($myType)
		{
			switch ($myType)
			{
				case 'page':
					$this->data->page['title'] = T_('Pages');
					$this->data->page['desc']  = T_('Check list of pages and to find your pages.'). ' '. T_('Also add or edit specefic static page.');
					break;
			}

		}

		// add back level to summary link
		$product_list_link        =  '<a href="'. \lib\url::here() .'" data-shortkey="121">'. T_('Back to dashboard'). '</a>';
		$this->data->page['desc'] .= ' | '. $product_list_link;



		$search_string            = \lib\utility::get('q');
		if($search_string)
		{
			$this->data->page['title'] .= ' | '. T_('Search for :search', ['search' => $search_string]);
		}

		$args =
		[
			'sort'  => \lib\utility::get('sort'),
			'order' => \lib\utility::get('order'),
		];

		if(\lib\utility::get('status'))
		{
			$args['status'] = \lib\utility::get('status');
		}

		if(\lib\utility::get('type'))
		{
			$args['type'] = \lib\utility::get('type');
		}
		else
		{
			$args['type'] = 'post';
		}

		if(\lib\utility::get('unittype'))
		{
			$args['unittype'] = \lib\utility::get('unittype');
		}

		if(!$args['order'])
		{
			$args['order'] = 'DESC';
		}


		if(!$args['sort'])
		{
			$args['sort'] = 'id';
		}


		$this->data->sort_link  = self::make_sort_link(\lib\app\posts::$sort_field, $this->data->modulePath);
		$this->data->dataTable = \lib\app\posts::list(\lib\utility::get('q'), $args);

		if(isset($this->controller->pagnation))
		{
			$this->data->pagnation = $this->controller->pagnation_get();
		}
	}
}
?>