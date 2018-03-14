<?php
namespace addons\content_su\transactions\add;

class view extends \addons\content_su\transactions\view
{
	public function config()
	{
		parent::config();
		$this->data->modulePath = \lib\url::here();

		$this->data->page['title'] = T_("Add new transactions");
		$this->data->page['desc'] = T_("Add new transactions for every one");


		$this->data->page['badge']['link'] = $this->data->modulePath. '/transactions';
		$this->data->page['badge']['text'] = T_('Back to transactions list');

	}

}
?>