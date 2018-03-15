<?php
namespace addons\content_cp\posts\home;


class view extends \addons\content_cp\posts\main\view
{
	public function config()
	{
		parent::config();

		$myType = \lib\request::get('type');

		$this->data->page['title'] = T_("Posts");
		$this->data->page['desc']  = T_('Check list of posts and search or filter in them to find your posts.'). ' '. T_('Also add or edit specefic post.');

		$this->data->page['badge']['link'] = \lib\url::this(). '/add'. $this->data->moduleType;
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



		$search_string            = \lib\request::get('q');
		if($search_string)
		{
			$this->data->page['title'] .= ' | '. T_('Search for :search', ['search' => $search_string]);
		}

		$args =
		[
			'sort'  => \lib\request::get('sort'),
			'order' => \lib\request::get('order'),
		];

		if(\lib\request::get('status'))
		{
			$args['status'] = \lib\request::get('status');
		}

		if(\lib\request::get('type'))
		{
			$args['type'] = \lib\request::get('type');
		}
		else
		{
			$args['type'] = 'post';
		}

		if(\lib\request::get('unittype'))
		{
			$args['unittype'] = \lib\request::get('unittype');
		}

		if(!$args['order'])
		{
			$args['order'] = 'DESC';
		}


		if(!$args['sort'])
		{
			$args['sort'] = 'id';
		}


		$this->data->sort_link  = self::make_sort_link(\lib\app\posts::$sort_field, \lib\url::this());
		$this->data->dataTable = \lib\app\posts::list(\lib\request::get('q'), $args);

		if(isset($this->controller->pagnation))
		{
			$this->data->pagnation = $this->controller->pagnation_get();
		}
	}
}
?>