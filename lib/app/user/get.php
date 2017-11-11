<?php
namespace lib\app\user;
use \lib\debug;

trait get
{


	/**
	 * Gets the user.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The user.
	 */
	public static function get($_args, $_options = [])
	{

		\lib\app::variable($_args);

		$default_options =
		[
			'debug'          => true,
			'other_field'    => null,
			'other_field_id' => null,
		];

		if(!is_array($_options))
		{
			$_options = [];
		}

		$_options = array_merge($default_options, $_options);

		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'input' => \lib\app::request(),
			]
		];

		if(!\lib\user::id())
		{
			return false;
		}

		$id = \lib\app::request("id");
		$id = \lib\utility\shortURL::decode($id);
		if(!$id)
		{
			if($_options['debug'])
			{
				\lib\app::log('api:staff:id:shortname:not:set', \lib\user::id(), $log_meta);
				\lib\debug::error(T_("Store id or shortname not set"), 'id', 'arguments');
			}
			return false;
		}

		$get_contact            = [];
		$get_contact['user_id'] = $id;

		if($_options['other_field'] && $_options['other_field_id'])
		{
			$get_contact[$_options['other_field']] = $_options['other_field_id'];
		}

		$get_contact_detail = \lib\db\contacts::get($get_contact);

		if(is_array($get_contact_detail))
		{
			$result = array_column($get_contact_detail, 'value', 'key');
		}
		else
		{
			$result = [];
		}

		return $result;
	}
}
?>