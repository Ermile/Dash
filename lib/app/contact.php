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
	public static function add($_args, $_option = [])
	{
		\lib\app::variable($_args);

		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'input' => \lib\app::request(),
			]
		];

		$default_option =
		[
			'save_log' => true,
			'debug'    => true,
		];

		if(!is_array($_option))
		{
			$_option = [];
		}

		$_option = array_merge($default_option, $_option);

		$user_id = \lib\app::request('user_id');
		$user_id = \lib\utility\shortURL::decode($user_id);
		if(!$user_id)
		{
			\lib\app::log("api:contact:user:id:not:set", \lib\user::id(), $log_meta);
			debug::error(T_("User id not set"), 'user_id');
			return false;
		}

		$all_request = \lib\app::request();

		$insert_contact = [];

		$in_user_db =
		[
			'mobile',
			'displayname',
			'title',
			'avatar',
			'status',
			'gender',
			'type',
			'email',
			'parent',
			'permission',
			'username',
			'pin',
			'ref',
			'twostep',
			'unit_id',
			'language',
		];

		foreach ($all_request as $key => $value)
		{
			if(!in_array($key, $in_user_db))
			{
				$value = trim($value);
				if(isset($value))
				{
					if(mb_strlen($key) >= 100)
					{
						\lib\app::log("api:contact:$key:the:key:max:length", \lib\user::id(), $log_meta);
						debug::error(T_("Key of contact is too large"), $key);
						return false;
					}

					if(mb_strlen($value) >= 100)
					{
						\lib\app::log("api:contact:$key:max:length", \lib\user::id(), $log_meta);
						debug::error(T_("Store name of contact can not be null"), $key);
						return false;
					}

					$contact[] =
					[
						'user_id' => $user_id,
						'key'     => $key,
						'value'   => $value,
					];
				}
			}
		}

		if(!empty($insert_contact))
		{
			\lib\db\contacts::insert_multi($insert_contact);
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