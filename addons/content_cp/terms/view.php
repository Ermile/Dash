<?php
namespace addons\content_cp\terms;


class view extends \addons\content_cp\main\view
{
	public function config()
	{
		$this->data->page['title'] = T_("Terms");
		$this->data->page['desc']  = T_("check terms and add some new terms");

		// $this->data->page['badge']['link'] = $this->url('baseFull'). '/service';
		// $this->data->page['badge']['text'] = T_('Back to service request list');

		$this->data->bodyclass       = 'unselectable siftal';


		$args =
		[
			'order'          => \lib\utility::get('order'),
			'sort'           => \lib\utility::get('sort'),
		];

		if(!$args['order'])
		{
			$args['order'] = 'DESC';
		}


		$search_string            = \lib\utility::get('q');

		if($search_string)
		{
			$this->data->page['title'] = T_('Search'). ' '.  $search_string;
		}

		$export = false;
		if(\lib\utility::get('export') === 'true')
		{
			$export = true;
			$args['pagenation'] = false;
		}

		$this->data->dataTable = \lib\app\term::list($search_string, $args);

		if($export)
		{
			\lib\utility\export::csv(['name' => 'export_service', 'data' => $this->data->dataTable]);
		}

		$this->data->sort_link = self::make_sort_link(\lib\app\term::$sort_field, $this->url('baseFull'). '/terms');

		if(isset($this->controller->pagnation))
		{
			$this->data->pagnation = $this->controller->pagnation_get();
		}



	}

	public function view_edit()
	{
		if(\lib\utility::get('edit'))
		{
			$this->data->edit_mode = true;

			$id = \lib\utility::get('edit');
			$id = \lib\utility\shortURL::decode($id);

			$x = $this->data->datarow = \lib\db\terms::get(['id' => $id, 'limit' => 1]);


			if(!$this->data->datarow)
			{
				\lib\error::page(T_("Id not found"));
			}
		}
	}
}
?>
