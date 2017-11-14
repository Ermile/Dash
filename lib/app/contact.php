<?php
namespace lib\app;
use \lib\utility;
use \lib\debug;

/**
 * Class for contact.
 */
class contact
{


	/**
	 * check args
	 *
	 * @return     array|boolean  ( description_of_the_return_value )
	 */
	public static function merge($_args, $_option = [])
	{
		\lib\app::variable($_args);

		$default_option =
		[
			'user_id'        => null,
			'other_field'    => null,
			'other_field_id' => null,
			'save_log'       => true,
			'debug'          => true,
		];

		if(!is_array($_option))
		{
			$_option = [];
		}

		$_option = array_merge($default_option, $_option);

		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'input' => \lib\app::request(),
			]
		];

		$_option = array_merge($default_option, $_option);

		if(!$_option['user_id'])
		{
			\lib\app::log("api:contact:user:id:not:set", \lib\user::id(), $log_meta);
			debug::error(T_("User id not set"), 'user_id');
			return false;
		}

		$all_request = \lib\app::request();

		if($_option['other_field'] && $_option['other_field_id'])
		{
			$get_old_contact_data =
			[
				$_option['other_field'] => $_option['other_field_id'],
				'user_id'               => $_option['user_id'],

			];
		}
		else
		{
			$get_old_contact_data =
			[
				'user_id'               => $_option['user_id'],
			];
		}

		$get_old_contact_data = \lib\db\contacts::get($get_old_contact_data);

		$exist_key   = [];
		$exist_value = [];

		if(is_array($get_old_contact_data))
		{
			$exist_key   = array_column($get_old_contact_data, 'key', 'id');
			$exist_value = array_column($get_old_contact_data, 'value', 'key');
		}

		$must_insert = [];
		$must_update = [];
		$must_delete = [];

		foreach ($all_request as $key => $value)
		{
			$value = trim($value);
			if(!isset($value))
			{
				continue;
			}

			if(mb_strlen($key) >= 100)
			{
				\lib\app::log("api:contact:$key:the:key:max:length", \lib\user::id(), $log_meta);
				debug::error(T_("Key of contact is too large"), $key);
				return false;
			}

			if(mb_strlen($value) >= 100)
			{
				\lib\app::log("api:contact:$key:max:length", \lib\user::id(), $log_meta);
				debug::error(T_("Contact value is too large."), $key);
				return false;
			}

			if(in_array($key, $exist_key))
			{
				// contact by this key was exist
				// if need update it
				if(array_key_exists($key, $exist_value) && $exist_value[$key] == $value)
				{
					// need less to update it
				}
				else
				{
					$must_update[] =
					[
						'args' => ['value' => $value],
						'id'   => array_search($key, $exist_key),
					];
				}
			}
			else
			{
				// add new record of contact
				if($_option['other_field'] && $_option['other_field_id'])
				{
					$must_insert[] =
					[
						$_option['other_field'] => $_option['other_field_id'],
						'user_id'               => $_option['user_id'],
						'key'                   => $key,
						'value'                 => $value,
					];
				}
				else
				{
					$must_insert[] =
					[
						'user_id' => $_option['user_id'],
						'key'     => $key,
						'value'   => $value,
					];
				}
			}
		}

		if(!empty($must_update))
		{
			foreach ($must_update as $key => $value)
			{
				\lib\db\contacts::update($value['args'], $value['id']);
			}
		}

		if(!empty($must_insert))
		{
			\lib\db\contacts::insert_multi($must_insert);
		}

		return true;

	}


	/**
	 * ready data of contact to load in api
	 *
	 * @param      <type>  $_data  The data
	 */
	public static function ready($_data)
	{
		$result = [];
		foreach ($_data as $key => $value)
		{

			switch ($key)
			{
				case 'id':
				case 'creator':
				case 'user_id':
				// case 'parent':
					if(isset($value))
					{
						$result[$key] = \lib\utility\shortURL::encode($value);
					}
					else
					{
						$result[$key] = null;
					}
					break;


				default:
					$result[$key] = $value;
					break;
			}
		}

		return $result;
	}

}
?>