<?php
namespace addons\content_cp\attachments\home;


class view extends \addons\content_cp\main\view
{
	public function config()
	{

		$this->data->page['title'] = T_("Attachemnts list");
		$this->data->page['desc']  = T_('Check list of attachments and search or filter in them to find your attachments.'). ' '. T_('Also add or edit specefic attachments.');
		// add back level to summary link
		$this->data->modulePath = $this->url('baseFull'). '/attachments';

		// $this->data->page['badge']['link'] = $this->data->modulePath. '/add';
		// $this->data->page['badge']['text'] = T_('Add new attachments');

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

		$args['type'] = 'attachment';

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