<?php
namespace dash\utility\location;

trait tools
{
	/**
	 * get the cost detail of country
	 *
	 * @example \dash\utility\location\countries::get('id', 107, 'name')
	 * @return string "Iran"
	 *
	 * @example \dash\utility\location\countries::get('name', 'Iran', 'id')
	 * @return string "107"
	 *
	 * @example \dash\utility\location\countries::get('name', 'Iran', ['id','language'])
	 * @return array ['id' => "107", 'language' => "fa-IR"]
	 *
	 * @param      string      		  $_type     The type
	 * @param      string		      $_cost     The cost
	 * @param      array|string       $_request  The request
	 *
	 * @return     array|string  ( description_of_the_return_value )
	 */
	public static function get($_type, $_cost, $_request = null)
	{

		foreach (self::$data as $key => $value)
		{
			if(isset(self::$data[$key][$_type]) && self::$data[$key][$_type] == $_cost)
			{
				if($_request && !is_array($_request))
				{
					if(isset(self::$data[$key][$_request]))
					{
						if($_request == "localname" && self::$data[$key][$_request] == '')
						{
							return self::$data[$key]['name'];
						}
						return self::$data[$key][$_request];
					}
					else
					{
						return null;
					}
				}

				if($_request && is_array($_request))
				{
					$result = [];
					foreach ($_request as $k => $v) {
						if(isset(self::$data[$key][$v]))
						{
							if($v == "localname" && self::$data[$key][$v] == '')
							{
								$result[$v] = self::$data[$key]['name'];
							}
							else
							{
								$result[$v] = self::$data[$key][$v];
							}
						}
						else
						{
							$result[$v] = null;
						}
					}
					return $result;
				}
				else
				{
					return self::$data[$key];
				}
			}
			elseif(isset(self::$data[$_type]))
			{

				if($_request && !is_array($_request))
				{
					if(isset(self::$data[$_type][$_request]))
					{
						if($_request == "localname" && self::$data[$_type][$_request] == '')
						{
							return self::$data[$_type]['name'];
						}
						return self::$data[$_type][$_request];
					}
					else
					{
						return null;
					}
				}

				if($_request && is_array($_request))
				{
					$result = [];
					foreach ($_request as $k => $v) {
						if(isset(self::$data[$_type][$v]))
						{
							if($v == "localname" && self::$data[$_type][$v] == '')
							{
								$result[$v] = self::$data[$_type]['name'];
							}
							else
							{
								$result[$v] = self::$data[$_type][$v];
							}
						}
						else
						{
							$result[$v] = null;
						}
					}
					return $result;
				}
				else
				{
					return self::$data[$_type];
				}
			}
		}
	}

	public static function html_data()
	{

		$countryList = \dash\utility\location\countres::$data;
		\dash\data::countryList($countryList);

		$cityList    = \dash\utility\location\cites::$data;
		$proviceList = \dash\utility\location\provinces::key_list('localname');

		$new = [];
		foreach ($cityList as $key => $value)
		{
			$temp = '';

			if(isset($value['province']) && isset($proviceList[$value['province']]))
			{
				$temp .= $proviceList[$value['province']]. ' - ';
			}
			if(isset($value['localname']))
			{
				$temp .= $value['localname'];
			}
			$new[$key] = $temp;
		}
		asort($new);

		\dash\data::cityList($new);
	}


	public static function fix_key($_data, $_field, $_trans = false)
	{
		$result = [];

		if(is_array($_data))
		{
			if($_field === 'name-localname')
			{
				foreach ($_data as $key => $value)
				{
					$myKey = $key;

					if(isset(self::$data[$key]['name']))
					{
						if($_trans)
						{
							$myKey = T_(self::$data[$key]['name']);
						}
						else
						{
							$myKey = self::$data[$key]['name'];
						}

						if(isset(self::$data[$key]['localname']) && self::$data[$key]['localname'])
						{
							if(\dash\utility\filter::slug($myKey) != \dash\utility\filter::slug(self::$data[$key]['localname']))
							{
								$myKey .= ' - '. self::$data[$key]['localname'];
							}
						}
					}
					$result[$myKey] = $value;
				}
			}
			else
			{
				foreach ($_data as $key => $value)
				{
					$myKey = $key;

					if(isset(self::$data[$key][$_field]))
					{
						if($_trans)
						{
							$myKey = T_(self::$data[$key][$_field]);
						}
						else
						{
							$myKey = self::$data[$key][$_field];
						}
					}
					$result[$myKey] = $value;
				}
			}
		}
		elseif(is_string($_data))
		{
			if($_field === 'name-localname')
			{
				if(isset(self::$data[$_data]['name']))
				{
					if($_trans)
					{
						$_data = T_(self::$data[$_data]['name']);
					}
					else
					{
						$_data = self::$data[$_data]['name'];
					}

					if(isset(self::$data[$_data]['localname']) && self::$data[$_data]['localname'])
					{
						if(\dash\utility\filter::slug($_data) != \dash\utility\filter::slug(self::$data[$_data]['localname']))
						{
							$_data .= ' - '. self::$data[$_data]['localname'];
						}
					}
				}
				$result = $_data;

			}
			else
			{
				if(isset(self::$data[$_data][$_field]))
				{
					if($_trans)
					{
						$_data = T_(self::$data[$_data][$_field]);
					}
					else
					{
						$_data = self::$data[$_data][$_field];
					}
				}

				$result = $_data;
			}
		}

		return $result;
	}


	public static function key_list($_colomn)
	{
		$result = [];
		foreach (self::$data as $key => $value)
		{
			if(array_key_exists($_colomn, $value))
			{
				$result[$key] = $value[$_colomn];
			}
		}
		return $result;
	}


	/**
	 * get list of country
	 */
	public static function list($_field, $_field2 = null)
	{
		$result = [];

		foreach (self::$data as $key => $value)
		{
			if($_field2)
			{
				if($_field2 == "localname" && $value['localname'] == '')
				{
					if(array_key_exists($_field, $value) && array_key_exists('name', $value))
					{
						$result[$value[$_field]] = $value['name'];
					}
				}
				elseif(preg_match("/^.*(name).*(localname).*$/", $_field2))
				{
					if($value['localname'] == '')
					{
						if(array_key_exists($_field, $value) && array_key_exists('name', $value) && array_key_exists('localname', $value))
						{
							$result[$value[$_field]] = str_replace('localname', $value['name'], $_field2);
							$result[$value[$_field]] = str_replace('name', $value['name'], $_field2);
						}
					}
					else
					{
						if(array_key_exists($_field, $value) && array_key_exists('name', $value) && array_key_exists('localname', $value))
						{
							$tmep = str_replace('localname', $value['localname'], $_field2);
							$tmep = str_replace('name', $value['name'], $tmep);
							$result[$value[$_field]] = $tmep;
						}
					}
				}
				else
				{
					if(array_key_exists($_field2, $value) && array_key_exists($_field, $value))
					{
						$result[$value[$_field]] = $value[$_field2];
					}
				}
			}
			else
			{
				if($_field == "localname" && $value['localname'] == '')
				{
					if(array_key_exists('name', $value))
					{
						$result[] = $value['name'];
					}
				}
				else
				{
					if(array_key_exists($_field, $value))
					{
						$result[] = $value[$_field];
					}
				}
			}
		}
		return $result;
	}


	/**
	 * check country name exist of no
	 *
	 * @param      <type>   $_name  The name
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function check($_name)
	{
		if(array_key_exists($_name, self::$data))
		{
			return true;
		}

		return false;
	}


	public static function get_localname($_key, $_trans = false)
	{
		$name = null;
		if(!$_trans && isset(self::$data[$_key]['localname']))
		{
			$name = self::$data[$_key]['localname'];
		}
		elseif(isset(self::$data[$_key]['name']))
		{
			$name = self::$data[$_key]['name'];
		}

		if($name && $_trans)
		{
			return T_($name);
		}

		return $name;
	}


	// search in field and return the key
	public static function get_key($_value, $_field = 'localname')
	{
		$list   = array_combine(array_keys(self::$data), array_column(self::$data, $_field));
		$result = array_search($_value, $list);
		return $result ? $result : null;
	}


	/**
	 * Searches for the first match.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function search($_args, $_cost = null)
	{
		if(!is_array($_args))
		{
			return false;
		}

		$result = [];

		foreach (self::$data as $key => $data)
		{
			foreach ($_args as $field => $value)
			{
				if(array_key_exists($field, $data))
				{
					if($data[$field] === $value)
					{
						array_push($result, $data);
					}
				}
			}
		}
		if($_cost)
		{
			$result = array_column($result, $_cost);
		}
		return $result;
	}
}
?>