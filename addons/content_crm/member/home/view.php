<?php
namespace content_crm\member\home;

class view
{

	public static function config()
	{
		\dash\data::page_title(T_("List of users"));
		\dash\data::page_desc(T_('Some detail about your users!'));
		\dash\data::page_desc(T_('Check list of members and search or filter in them to find your user.'));
		\dash\data::page_desc(\dash\data::page_desc(). ' '. T_('Also add or edit specefic user.'));
		\dash\data::page_pictogram('users');

		$args =
		[
			'sort'  => \dash\request::get('sort'),
			'order' => \dash\request::get('order'),
		];

		$search_string            = \dash\request::get('q');
		if($search_string)
		{
			\dash\data::page_title(\dash\data::page_title(). ' | '. T_('Search for :search', ['search' => $search_string]));
		}

		\dash\data::badge_link(\dash\url::this(). '/add');
		\dash\data::badge_text(T_('Add new user'));


		if(!$args['order'])
		{
			$args['order'] = 'desc';
		}

		if(\dash\request::get('status'))
		{
			$args['status'] = \dash\request::get('status');
		}

		if(\dash\request::get('findusername'))
		{
			$args['username'] = \dash\request::get('findusername');
		}

		if(\dash\request::get('findmobile') || \dash\request::get('findmobile') == "0")
		{
			$args['mobile'] = \dash\request::get('findmobile');
		}


		if(\dash\request::get('findemail'))
		{
			$args['email'] = \dash\request::get('findemail');
		}

		// if(\dash\request::get('android_uniquecode'))
		// {
		// 	$args['android_uniquecode'] = \dash\request::get('android_uniquecode');
		// }


		if(\dash\request::get('permission'))
		{
			$args['permission'] = \dash\request::get('permission');
		}

		self::advance_filter($args);


		$sortLink = \dash\app\sort::make_sortLink(\dash\app\user::$sort_field, \dash\url::this());

		if(\dash\permission::supervisor() && in_array(\dash\request::get('duplicate'), ['mobile', 'chatid', 'email', 'username','android_uniquecode']))
		{
			$args['check_duplicate'] = \dash\request::get('duplicate');
		}

		$dataTable = \dash\app\user::list(\dash\request::get('q'), $args);

		if(!is_array($dataTable))
		{
			$dataTable = [];
		}

		\dash\data::sortLink($sortLink);
		\dash\data::dataTable($dataTable);


		$check_empty_datatable = $args;
		unset($check_empty_datatable['sort']);
		unset($check_empty_datatable['order']);

		// set dataFilter
		$dataFilter = \dash\app\sort::createFilterMsg($search_string, $check_empty_datatable);
		\dash\data::dataFilter($dataFilter);

	}


	private static function advance_filter(&$args)
	{
		$allow_filter =
		[
			'username',
			'avatar',
			'displayname',
			'mobile',
			'email',
			'password',
			'twostep',
			'permission',
			'language',

		];

		foreach ($allow_filter as $key => $value)
		{
			if(\dash\request::get($value) === 'yes')
			{
				$args[$value] = [" IS ", " NOT NULL "];
			}
			elseif(\dash\request::get($value) === 'no')
			{
				$args[$value] = [" IS ", " NULL "];
			}
		}
	}
}
?>