<?php
namespace content_cp\posts\edit;

class view extends \addons\content_cp\posts\main\view
{
	public function config()
	{
		parent::config();

		$id = \dash\request::get('id');

		$detail = \dash\app\posts::get($id);
		if(!$detail)
		{
			\dash\header::status(403, T_("Invalid id"));
		}

		$this->data->dataRaw = $detail;
		$this->data->cat_list = \dash\app\term::cat_list();



		$this->data->page['title'] = T_("Edit post");
		$this->data->page['desc']  = T_("You can change everything, change url and add gallery or some other change");

		$this->data->page['badge']['link'] = \dash\url::this(). $this->data->moduleType;
		$this->data->page['badge']['text'] = T_('Back to list of posts');

		$myType = \dash\request::get('type');
		if($myType)
		{
			switch ($myType)
			{
				case 'page':
					$this->data->page['title'] = T_('Edit page');
					$this->data->page['badge']['text'] = T_('Back to list of pages');
					break;
			}
		}

	}
}
?>