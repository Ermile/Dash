<?php
namespace content_support;

class view
{
	public static function config()
	{
		\dash\data::include_adminPanel(true);
		\dash\data::include_css(false);
		\dash\data::include_js(false);

		\dash\data::badge_shortkey(120);
		\dash\data::badge2_shortkey(121);

		\dash\data::include_chart(true);
		\dash\data::display_admin('content_support/layout.html');

		\dash\data::maxUploadSize(\dash\utility\upload::max_file_upload_size(true));

		self::acceessModeDetector();
		self::sidebarDetail();
	}


	private static function acceessModeDetector()
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


	public static function sidebarDetail($_all = false)
	{
		$args               = [];
		$args_tag           = [];

		$args['comments.type']       = 'ticket';
		$args['comments.parent']     = null;

		if(!\dash\data::haveSubdomain())
		{
			if(\dash\data::subdomain())
			{
				$args['comments.subdomain']    = \dash\url::subdomain();
			}
			else
			{
				$args['comments.subdomain']    = null;
			}
		}

		$result               = [];

		if(\dash\data::accessMode() === 'mine')
		{
			$args['comments.user_id'] = \dash\user::id();
			$result['all']   = $result['mine']  = \dash\db\comments::get_count(array_merge($args,[]));
		}
		else
		{
			$result['all']      = \dash\db\comments::get_count(array_merge($args, []));
		}

		$result['answered']       = \dash\db\comments::get_count(array_merge($args,['status' => 'answered']));
		$result['awaiting']       = \dash\db\comments::get_count(array_merge($args, ['status' => 'awaiting']));
		$result['open']           = intval($result['answered']) + intval($result['awaiting']);

		$result['archived'] = \dash\db\comments::get_count(array_merge($args,['status' => 'close']));

		$args_tag = $args;
		if($_all)
		{
			unset($args['parent']);
			$result['message']    = \dash\db\comments::get_count($args);

			unset($args['status']);
			$args['parent']       = null;
			$result['avgfirst']   = \dash\db\comments::ticket_avg_first($args);
			$result['avgarchive'] = \dash\db\comments::ticket_avg_archive($args);
		}

		$tags = \dash\db\comments::ticket_tag($args_tag);
		$result['tags'] = array_map(['\dash\app\term', 'ready'], $tags);


		\dash\data::sidebarDetail($result);
		return $result;

	}



	public static function dataList($_args)
	{
		$args = $_args;

		\dash\data::haveSubdomain(true);

		switch (\dash\data::accessMode())
		{
			case 'mine':
				$args['user_id'] = \dash\user::id();
				break;

			case 'all':
				\dash\permission::access('supportTicketViewAll');
				break;

			case 'manage':
				\dash\permission::access('supportTicketView');

				\dash\data::haveSubdomain(false);

				if(\dash\url::subdomain())
				{
					$args['comments.subdomain']    = \dash\url::subdomain();
				}
				else
				{
					$args['comments.subdomain']    = null;
				}
				break;

			default:
				break;
		}

		$dataTable = \dash\app\ticket::list(null, $args);
		$dataTable = array_map(['self', 'tagDetect'], $dataTable);

		\dash\data::dataTable($dataTable);
	}


	public static function tagDetect($_data)
	{
		if(isset($_data['tag']))
		{
			$tag = $_data['tag'];
			$tag = explode(',', $tag);
			$_data['tag'] = $tag;
		}
		return $_data;
	}
}
?>