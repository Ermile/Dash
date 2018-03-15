<?php
namespace addons\content_cp\terms;


class view extends \addons\content_cp\main\view
{
	public function config()
	{

		$this->data->page['title'] = T_("Terms");
		$this->data->page['desc']  = T_("Check terms and filter by type or view and edit some terms");

		$this->data->page['badge']['link'] = \lib\url::here();
		$this->data->page['badge']['text'] = T_('Back to dashboard');

		$myType = \lib\utility::get('type');
		if($myType)
		{
			switch ($myType)
			{
				case 'cat':
				case 'category':
					$this->data->page['title'] = T_('Categories');
					$this->data->page['desc']  = T_("Check categories and add or edit some new category");
					break;

				case 'tag':
					$this->data->page['title'] = T_('Tags');
					$this->data->page['desc']  = T_("Check tags and add or edit some new tag");
					break;
			}
		}





		$args =
		[
			'order' => \lib\utility::get('order'),
			'sort'  => \lib\utility::get('sort'),
		];

		if(!$args['order'])
		{
			$args['order'] = 'DESC';
		}

		if($myType)
		{
			if($myType === 'category')
			{
				$args['type'] = 'cat';
			}
			else
			{
				$args['type'] = $myType;
			}
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
			$this->data->datarow = \lib\app\term::get($id);

			if(!$this->data->datarow)
			{
				\lib\error::page(T_("Id not found"));
			}
		}
	}
}
?>
