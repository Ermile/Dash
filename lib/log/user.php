<?php
namespace dash\log;

trait user
{

	private static function detect_user($_user_id)
	{
		$all_user_detail = [];
		if($_user_id && is_numeric($_user_id))
		{
			// force send to this user
			if(intval($_user_id) === intval(\dash\user::id()))
			{
				// neddless to load userdetail again
				$all_user_detail[] = \dash\user::detail();
			}
			else
			{
				$find_user = \dash\db\users::get_by_id($_user_id);
				if(!$find_user)
				{
					return false;
				}
				$all_user_detail[] = $find_user;
			}
		}
		else
		{
			$send_to = self::detail('send_to');
			if(!$send_to || !is_array($send_to))
			{
				return false;
			}

			if(in_array('supervisor', $send_to))
			{
				$load_all_supervisor = \dash\db\users::get(['permission' => 'supervisor', 'status' => 'active']);
				if($load_all_supervisor)
				{
					$all_user_detail = array_merge($all_user_detail, $load_all_supervisor);
				}
			}

			if(in_array('admin', $send_to))
			{
				$load_all_admin = \dash\db\users::get(['permission' => 'admin', 'status' => 'active']);
				if($load_all_admin)
				{
					$all_user_detail = array_merge($all_user_detail, $load_all_admin);
				}
			}
		}

		if(empty($all_user_detail))
		{
			return false;
		}

		// to remove duplicate if exist
		$all_user_detail       = array_combine(array_column($all_user_detail, 'id'), $all_user_detail);
		self::$all_user_detail = $all_user_detail;
		return true;
	}


}
?>
