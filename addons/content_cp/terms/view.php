<?php
namespace content_cp\terms;


class view extends \content_cp\main2\view
{
	public function config()
	{
		$this->data->page['title'] = T_("Terms");
		$this->data->page['desc']  = T_("check terms and add some new terms");

		// $this->data->page['badge']['link'] = $this->url('baseFull'). '/service';
		// $this->data->page['badge']['text'] = T_('Back to service request list');

		$this->data->bodyclass       = 'unselectable siftal';

		$this->data->need = \lib\app\need::list('expertise');

	}

	public function view_edit()
	{
		if(\lib\utility::get('edit'))
		{
			$this->data->edit_mode = true;
			$id = \lib\utility::get('edit');
			$this->data->product_detail = \lib\db\needs::get(['id' => $id, 'limit' => 1]);
			if(!$this->data->product_detail)
			{
				\lib\error::page(T_("Id not found"));
			}
		}
	}
}
?>
