<?php
namespace addons\content_cp\posts\home;


class view extends \addons\content_cp\main\view
{
	public function config()
	{
		parent::config();

		$this->data->page['title'] = T_("Posts list");
		$this->data->page['desc']  = T_('Check list of posts and search or filter in them to find your posts.'). ' '. T_('Also add or edit specefic posts.');
		// add back level to summary link
		$this->data->modulePath = $this->url('baseFull'). '/posts';

		$this->data->page['badge']['link'] = $this->data->modulePath. '/add';
		$this->data->page['badge']['text'] = T_('Add new posts');

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

		if(\lib\utility::get('unittype'))
		{
			$args['unittype'] = \lib\utility::get('unittype');
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