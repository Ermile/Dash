<?php
namespace content_crm;

class view
{
	public static function config()
	{

		\dash\data::include_adminPanel(true);
		\dash\data::include_css(false);
		\dash\data::include_js(false);

		\dash\data::include_highcharts(true);


		\dash\data::include_editor(true);
		\dash\data::badge_shortkey(120);
		\dash\data::badge2_shortkey(121);

		\dash\data::maxUploadSize(\dash\utility\upload::max_file_upload_size(true));
	}


	public static function make_sort_link($_field, $_url)
	{
		$get = \dash\request::get();
		if(!is_array($get))
		{
			$get = [];
		}

		$default_get =
		[
			'q'     => null,
			'sort'  => null,
			'order' => null,
		];

		$get          = array_merge($default_get, $get);
		$get['order'] = mb_strtolower($get['order']);
		$get['sort']  = mb_strtolower($get['sort']);

		$link = [];

		foreach ($_field as $key => $field)
		{
			$temp_link         = [];
			$temp_link['sort'] = $field;

			if($field === $get['sort'])
			{
				$temp_link['order'] = 'asc';
				if($get['order'] === 'asc')
				{
					$temp_link['order'] = 'desc';
				}
				$link[$field]['order'] = $temp_link['order'] === 'asc' ? 'desc' : 'asc';
			}
			else
			{
				$temp_link['order']    = 'asc';
				$link[$field]['order'] = null;
			}

			$temp_link['q'] = $get['q'];

			if(is_array(\dash\request::get()))
			{
				foreach (\dash\request::get() as $query_key => $query_value)
				{
					if(!in_array($query_key, ['q', 'sort', 'order']))
					{
						$temp_link[$query_key] = $query_value;
					}
				}
			}

			$link[$field]['link'] = $_url . '?'.  http_build_query($temp_link);
		}
		return $link;
	}


	public static function createFilterMsg($_searchText, $_filterArray)
	{
		$result = null;

		if($_searchText)
		{
			$result = T_('Search with keyword :search', ['search' => '<b>'. $_searchText. '</b>']);
		}

		if($_filterArray)
		{
			$result .= ' '. T_('with condition'). ' ';
			$index  = 0;
			foreach ($_filterArray as $key => $value)
			{
				if($result && $index > 0)
				{
					$result .= T_(', ');
				}
				if($value === 1)
				{
					$value = 'enable';
				}
				elseif($value === 0)
				{
					$value = 'disable';
				}
				if(is_numeric($value))
				{
					$value = \dash\utility\human::fitNumber($value);
				}
				$result .= T_($key) . ' <b>'. T_(ucfirst($value)). '</b>';
				$index++;
			}
		}

		return $result;
	}
}
?>