<?php
namespace content_cp\posts\home;


class view extends \addons\content_cp\posts\main\view
{
	public function config()
	{
		parent::config();

		$myType = \dash\request::get('type');

		$this->data->page['title'] = T_("Posts");
		$this->data->page['desc']  = T_('Check list of posts and search or filter in them to find your posts.'). ' '. T_('Also add or edit specefic post.');

		$this->data->page['badge']['link'] = \dash\url::this(). '/add'. $this->data->moduleType;
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
		$product_list_link        =  '<a href="'. \dash\url::here() .'" data-shortkey="121">'. T_('Back to dashboard'). '</a>';
		$this->data->page['desc'] .= ' | '. $product_list_link;



		$search_string            = \dash\request::get('q');
		if($search_string)
		{
			$this->data->page['title'] .= ' | '. T_('Search for :search', ['search' => $search_string]);
		}

		$args =
		[
			'sort'  => \dash\request::get('sort'),
			'order' => \dash\request::get('order'),
		];

		if(\dash\request::get('status'))
		{
			$args['status'] = \dash\request::get('status');
		}

		if(\dash\request::get('type'))
		{
			$args['type'] = \dash\request::get('type');
		}
		else
		{
			$args['type'] = 'post';
		}

		if(\dash\request::get('unittype'))
		{
			$args['unittype'] = \dash\request::get('unittype');
		}

		if(!$args['order'])
		{
			$args['order'] = 'DESC';
		}


		if(!$args['sort'])
		{
			$args['sort'] = 'id';
		}


		$this->data->sort_link  = \content_cp\view::::make_sort_link(\dash\app\posts::$sort_field, \dash\url::this());
		$this->data->dataTable = \dash\app\posts::list(\dash\request::get('q'), $args);

		if(isset($this->controller->pagnation))
		{
			$this->data->pagnation = $this->controller->pagnation_get();
		}
	}
}
?>