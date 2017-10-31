<?php
namespace lib\app;
use \lib\utility;
use \lib\debug;

/**
 * Class for contact.
 */
class contact
{

	use contact\add;
	use contact\edit;
	use contact\datalist;
	use contact\get;


	/**
	 * check args
	 *
	 * @return     array|boolean  ( description_of_the_return_value )
	 */
	private static function check($_option = [])
	{
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

		$contact_field =
		[
			'title',
			'displayname',
			'firstname',
			'lastname',
			'postion',
			'personnel_code',
			'avatar',
			'status',
			'nationalcode',
			'father',
			'birthday',
			'gender',
			'type',
			'marital',
			'child',
			'birthplace',
			'shfrom',
			'shcode',
			'education',
			'job',
			'passportcode',
			'passportexpire',
			'paymentaccountnumber',
			'shaba',
			'cardnumber',
			'email',
			'parent',
			'permission',
			'username',
			'group',
			'pin',
			'ref',
			'twostep',
			'notification',
			'setup',
			'nationality',
			'region',
			'insurancetype',
			'insurancecode',
			'dependantscount',
			'language',
		];

		$all_request = \lib\app::request();

		$args = [];

		foreach ($all_request as $key => $value)
		{
			if(in_array($key, $contact_field))
			{
				$value = trim($value);
				if(isset($value))
				{
					if(mb_strlen($value) >= 100)
					{
						\lib\app::log("api:contact:$key:max:length", \lib\user::id(), $log_meta);
						debug::error(T_("Store name of contact can not be null"), $key);
						return false;
					}

					$args[$key] = $value;
				}
			}
		}

		return $args;
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