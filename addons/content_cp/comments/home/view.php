<?php
namespace addons\content_cp\comments\home;


class view extends \addons\content_cp\main\view
{
	public function config()
	{

		$this->data->page['title'] = T_("Comments");
		$this->data->page['desc']  = T_('Check list of comments and search or filter in them to find your comments.'). ' '. T_('Also add or edit specefic comments.');

		// $this->data->page['badge']['link'] = $this->data->modulePath. '';
		// $this->data->page['badge']['text'] = T_('Add new :val', ['val' => $myType]);

		// add back level to summary link
		$product_list_link        =  '<a href="'. $this->url('baseFull') .'" data-shortkey="121">'. T_('Back to dashboard'). '</a>';
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
			$args['type'] = 'comment';
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

		$this->data->sort_link  = self::make_sort_link(\lib\app\comment::$sort_field, $this->data->modulePath);
		$this->data->dataTable = \lib\app\comment::list(\lib\utility::get('q'), $args);

		if(isset($this->controller->pagnation))
		{
			$this->data->pagnation = $this->controller->pagnation_get();
		}
	}
}
?>