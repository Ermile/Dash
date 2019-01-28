<?php
namespace content_support;

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


		\dash\data::display_admin('content_support/layout.html');

		\dash\data::maxUploadSize(\dash\utility\upload::max_file_upload_size(true));

		self::make_breadcrumb();

		self::acceessModeDetector();
	}

	public static function make_breadcrumb()
	{

		$myBreadCrumb = [];

		$myBreadCrumb[] =
		[
			"text"  => \dash\data::site_title(),
			"link"  => \dash\url::sitelang(),
			'title' => \dash\data::site_desc(),
			"icon"  => "home",
			'attr'  => 'data-direct',
		];

		if(\dash\url::module())
		{
			$myBreadCrumb[] =
			[
				"text"  => T_("Support"),
				"link"  => \dash\url::here(),
				'title' => T_("Support dashboard"),
			];
		}


		if(\dash\url::child())
		{
			switch (\dash\url::child())
			{
				case 'show':
				case 'add':
				case 'message':
					$myBreadCrumb[] =
					[
						"text"  => T_("Ticket"),
						"link"  => \dash\url::this(),
					];
					break;

				default:
					# nothing
					break;
			}
		}

		\dash\data::page_breadcrumb($myBreadCrumb);
	}


	public static function acceessModeDetector()
	{
		$selected_access = 'mine';
		$get_access      = \dash\request::get('access');
		if($get_access)
		{
			$selected_access = $get_access;
		}
		// if not exist show 412 error
		if(!in_array($selected_access, ['mine', 'all', 'manage']))
		{
			\dash\header::status(412, T_("Invalid access in url"));
		}

		// set data variables
		\dash\data::accessMode($selected_access);
		if($get_access)
		{
			\dash\data::accessGet('?access='. $get_access);
			\dash\data::accessGetAnd('&access='. $get_access);
		}

	}
}
?>