<?php
namespace addons\content_cp\comments\edit;

class view extends \addons\content_cp\main\view
{
	public function config()
	{


		$id = \lib\utility::get('id');

		$detail = \lib\app\comment::get($id);
		if(!$detail)
		{
			\lib\error::access(T_("Invalid id"));
		}

		$this->data->dataRaw               = $detail;
		$this->data->cat_list              = \lib\app\term::cat_list();

		$this->data->page['title']         = T_("Edit comment");
		$this->data->page['desc']          = T_("You can accept or reject the comment");

		$this->data->page['badge']['link'] = $this->data->modulePath;
		$this->data->page['badge']['text'] = T_('Back to list of comments');

	}
}
?>