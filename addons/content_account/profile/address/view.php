<?php
namespace content_account\profile\address;


class view
{

	public static function config()
	{
		\dash\data::page_title(T_('Edit your profile'));
		\dash\data::page_desc(T_('You can edit your profile.'));

		\dash\data::badge_link(\dash\url::base(). '/a');
		\dash\data::badge_text(T_('Back to dashbaord'));

		$id = \dash\user::id();

		if(!$id)
		{
			\dash\header::status(404, T_("Invalid user id"));
		}

		$user_detail = \dash\db\users::get_by_id($id);

		if(!$user_detail)
		{
			\dash\header::status(404, T_("User id not found"));
		}

		\dash\data::dataRow(\dash\app\user::ready($user_detail, true));

		$args               = [];
		$args['user_id']    = \dash\user::id();
		$args['pagenation'] = false;
		$dataTable          = \dash\app\address::list(null, $args);
		\dash\data::dataTable($dataTable);


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
}
?>
