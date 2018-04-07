<?php
namespace content_cp\terms;


class view extends \addons\content_cp\main\view
{
	public function config()
	{

		$this->data->page['title'] = T_("Terms");
		$this->data->page['desc']  = T_("Check terms and filter by type or view and edit some terms");

		$this->data->page['badge']['link'] = \dash\url::here();
		$this->data->page['badge']['text'] = T_('Back to dashboard');

		$myType = \dash\request::get('type');
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
			'order' => \dash\request::get('order'),
			'sort'  => \dash\request::get('sort'),
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

		$search_string            = \dash\request::get('q');

		if($search_string)
		{
			$this->data->page['title'] = T_('Search'). ' '.  $search_string;
		}

		$export = false;
		if(\dash\request::get('export') === 'true')
		{
			$export = true;
			$args['pagenation'] = false;
		}

		$this->data->dataTable = \dash\app\term::list($search_string, $args);

		if($export)
		{
			\dash\utility\export::csv(['name' => 'export_service', 'data' => $this->data->dataTable]);
		}

		if(isset($this->controller->pagnation))
		{
			$this->data->pagnation = $this->controller->pagnation_get();
		}

	}


	public function view_edit()
	{
		if(\dash\request::get('edit'))
		{
			$this->data->edit_mode = true;

			$id = \dash\request::get('edit');
			$this->data->datarow = \dash\app\term::get($id);

			if(!$this->data->datarow)
			{
				\dash\header::status(404, T_("Id not found"));
			}
		}
	}
}
?>
