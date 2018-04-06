<?php
namespace dash\app\user;


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

		\dash\app::variable($_args);

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
				'input' => \dash\app::request(),
			]
		];

		if(!\dash\user::id())
		{
			return false;
		}

		$id = \dash\app::request("id");
		$id = \dash\coding::decode($id);
		if(!$id)
		{
			if($_options['debug'])
			{
				\dash\app::log('api:staff:id:shortname:not:set', \dash\user::id(), $log_meta);
				\dash\notif::error(T_("Store id or shortname not set"), 'id', 'arguments');
			}
			return false;
		}

		$get_contact            = [];
		$get_contact['user_id'] = $id;

		if($_options['other_field'] && $_options['other_field_id'])
		{
			$get_contact[$_options['other_field']] = $_options['other_field_id'];
		}

		$get_contact_detail = \dash\db\contacts::get($get_contact);

		if(is_array($get_contact_detail))
		{
			$result = array_column($get_contact_detail, 'value', 'key');
			$result['fullname'] = '';

			if(isset($result['firstname']))
			{
				$result['fullname'] .= $result['firstname'];
			}

			if(isset($result['lastname']))
			{
				$result['fullname'] .= ' '. $result['lastname'];
			}
		}
		else
		{
			$result = [];
		}

		return $result;
	}
}
?>