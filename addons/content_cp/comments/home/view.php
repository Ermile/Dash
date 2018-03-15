<?php
namespace addons\content_cp\comments\home;


class view extends \addons\content_cp\main\view
{
	public function config()
	{

		$this->data->page['title'] = T_("Comments");
		$this->data->page['desc']  = T_('Check list of comments and search or filter in them to find your comments.'). ' '. T_('Also add or edit specefic comments.');

		// $this->data->page['badge']['link'] = \lib\url::this(). '';
		// $this->data->page['badge']['text'] = T_('Add new :val', ['val' => $myType]);

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
			$args['type'] = 'comment';
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

		$this->data->sort_link  = self::make_sort_link(\lib\app\comment::$sort_field, \lib\url::this());
		$this->data->dataTable = \lib\app\comment::list(\lib\request::get('q'), $args);

		if(isset($this->controller->pagnation))
		{
			$this->data->pagnation = $this->controller->pagnation_get();
		}
	}
}
?>