<?php
namespace addons\content_cp\posts\add;

class view extends \addons\content_cp\posts\main\view
{
	public function config()
	{
		parent::config();
		$this->data->cat_list = \lib\app\term::cat_list();


		$this->data->page['title'] = T_("Add new post");
		$this->data->page['desc']  = T_("Posts can contain keyword and category with title and descriptions.");

		$this->data->page['badge']['link'] = $this->data->modulePath. $this->data->moduleType;
		$this->data->page['badge']['text'] = T_('Back to list of posts');

		$myType = \lib\utility::get('type');
		if($myType)
		{
			switch ($myType)
			{
				case 'page':
					$this->data->page['title'] = T_('Add new page');
					$this->data->page['desc']  = T_("Add new static page like about or honors");

					$this->data->page['badge']['text'] = T_('Back to list of pages');
					break;
			}
		}

	}
}
?>