<?php
namespace dash\db;

/** sessions managing **/
class sessions
{
	/**
	 * this library work with sessions table
	 * v1.0
	 */


	/**
	 * generate code
	 *
	 * @param      string  $_user_id  The user identifier
	 *
	 * @return     string  ( description_of_the_return_value )
	 */
	public static function generate_code($_user_id)
	{
		$code =  'Ermile'. $_user_id. '_;)_'. time(). '(^_^)' . rand(1000, 9999);
		$code = \dash\utility::hasher($code, false);
		$code = \dash\safe::safe($code);
		return $code;
	}


	public static function get_count($_where = [])
	{
		return \dash\db\config::public_get_count('sessions', $_where, \dash\db::get_db_log_name());
	}


	/**
	 * insert sessions on database
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function insert($_args)
	{
		$set = \dash\db\config::make_set($_args);
		if(!trim($set))
		{
			return false;
		}

		return \dash\db::query("INSERT INTO sessions SET $set", \dash\db::get_db_log_name());
	}


	public static function remove_old_expire()
	{
		$date = date("Y-m-d H:i:s", strtotime("-30 days"));

		$query =
		"
			SELECT
				sessions.id AS `id`
			FROM
				sessions
			WHERE
				sessions.status = 'active' AND
				sessions.datecreated < '$date'
		";

		$get_list = \dash\db::get($query, 'id', false, \dash\db::get_db_log_name());
		if(!$get_list || !is_array($get_list) || empty($get_list))
		{
			return true;
		}

		$id = implode($get_list, ',');

		$query =
		"
			UPDATE
				sessions
			SET
				sessions.status = 'expire'
			WHERE
				sessions.id IN ($id)

		";

		$ok = \dash\db::query($query, \dash\db::get_db_log_name());
		if($ok)
		{
			\dash\log::set('AutoExpireSession', ['countsession' => count($get_list)]);
		}

	}


	/**
	* check session id is matched by user id
	*/
	public static function is_my_session($_session_id, $_user_id)
	{
		if(!$_session_id || !$_user_id || !is_numeric($_session_id) || !is_numeric($_user_id))
		{
			return false;
		}
		$query = "SELECT * FROM sessions WHERE user_id = $_user_id AND id = $_session_id LIMIT 1";
		return \dash\db::get($query, null, true, \dash\db::get_db_log_name());
	}


	/**
	 * get record is exist or no
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function get($_args)
	{
		if(\dash\temp::get('db_remember_me_query'))
		{
			return \dash\temp::get('db_remember_me_query_result');
		}

		$where = \dash\db\config::make_where($_args);
		if(!trim($where))
		{
			return false;
		}

		$get   = \dash\db::get("SELECT * FROM sessions WHERE $where LIMIT 1", null, true, \dash\db::get_db_log_name());
		\dash\temp::set('db_remember_me_query', true);
		\dash\temp::set('db_remember_me_query_result', $get);
		return $get;
	}


	/**
	 * check_code session code
	 *
	 * @param      <type>  $_code  The code
	 */
	public static function check_code($_code)
	{
		$get = self::get(['code' => $_code]);
		if(empty($get))
		{
			return false;
		}
		else
		{
			if(isset($get['status']))
			{
				switch ($get['status'])
				{
					case 'active':
						return true;
						break;

					default:
						return false;
						break;
				}
			}
		}
		return false;
	}


	/**
	 * Gets the cookie.
	 *
	 * @return     <type>  The cookie.
	 */
	public static function get_cookie()
	{
		return \dash\utility\cookie::read('remember_me_');
	}


	public static function update($_args, $_id)
	{
		return \dash\db\config::public_update('sessions',$_args, $_id, \dash\db::get_db_log_name());
	}


	/**
	 * Terminate the cookie.
	 *
	 * @param      <type>  $_code  The code
	 */
	public static function terminate_cookie($_check_cause = true)
	{
		$code = self::get_cookie();

		\dash\utility\cookie::delete("remember_me_", '/', self::cookie_domain());

		if($_check_cause)
		{

			$get_cookie =
			[
				'status'   => 'active',
				'code'     => addslashes($code),
			];

			$get  = self::get($get_cookie);

			if(isset($get['ip']) && intval($get['ip']) !== intval(\dash\server::ip(true)))
			{
				\dash\log::set('ipChangeRememberExpire');
			}

			if(isset($get['agent_id']) && intval($get['agent_id']) !== intval(\dash\agent::get(true)))
			{
				\dash\log::set('agentChangeRememberExpire');
			}
		}

	}

	// change status of cookie to disable
	// by search in code and user id
	public static function disable_cookie($_code, $_user_id)
	{
		$get =
		[
			'user_id' => $_user_id,
			'code'    => addslashes($_code),
		];

		$get = self::get($get);

		if(isset($get['id']))
		{
			self::update(['status' => 'disable'], $get['id']);
			\dash\log::set('invalidCookieAutoDisable');
		}
		else
		{
			\dash\log::set('notCookieFoundToDisalbe');
		}
	}


	private static function cookie_domain()
	{
		if(\dash\option::config('free_subdomain'))
		{
			$cookie_domain = \dash\url::subdomain(). '.'. \dash\url::domain();
		}
		else
		{
			$cookie_domain = '.'. \dash\url::domain();
		}
		return $cookie_domain;
	}


	/**
	 * Sets the cookie.
	 *
	 * @param      <type>  $_code  The code
	 */
	public static function set_cookie($_code)
	{

		setcookie("remember_me_", $_code, time() + (60*60*24*30), '/', self::cookie_domain());
	}


	public static function is_active($_code, $_user_id)
	{
		if($_code && is_numeric($_user_id))
		{
			$_code = addslashes($_code);
			$query = "SELECT * FROM sessions WHERE sessions.user_id = $_user_id AND sessions.status = 'active' AND sessions.code = '$_code' LIMIT 1";
			$get   = \dash\db::get($query, null, true, \dash\db::get_db_log_name());

			if(isset($get['status']))
			{
				// check ip and agent and if updated, update it
				$update_current = [];

				if(isset($get['ip']) && intval($get['ip']) !== intval(\dash\server::ip(true)))
				{
					\dash\log::set('sessionIPupdated', ['code' => isset($get['id']) ? $get['id'] : null]);
					$update_current['ip'] = \dash\server::ip(true);
				}

				// if(isset($get['agent_id']) && intval($get['agent_id']) !== intval(\dash\agent::get(true)))
				// {
				// 	$update_current['agent_id'] = \dash\agent::get(true);
				// }

				if(!empty($update_current))
				{
					self::update($update_current, $get['id']);
				}

				return true;
			}
			else
			{
				return false;
			}
		}
		return null;
	}


	/**
	 * Gets the user identifier.
	 *
	 * @return     <type>  The user identifier.
	 */
	public static function get_user_id_from_cookie()
	{
		$code = self::get_cookie();

		$get_cookie =
		[
			'status'   => 'active',
			'agent_id' => \dash\agent::get(true),
			'ip'       => \dash\server::ip(true),
			'code'     => addslashes($code),
		];
		$get  = self::get($get_cookie);

		if(isset($get['user_id']))
		{
			self::login($code);
			return (int) $get['user_id'];
		}
		return false;
	}

	/**
	* terminate one id
	*/
	public static function terminate_id($_id)
	{
		if(!$_id || !is_numeric($_id))
		{
			return false;
		}

		\dash\db::query("UPDATE sessions SET status = 'terminate' WHERE id = $_id LIMIT 1", \dash\db::get_db_log_name());
	}


	public static function terminate_all_other($_user_id)
	{
		$code = self::get_cookie();

		$where_code = null;

		if($code)
		{
			$where_code = " AND sessions.code != '$code' ";
		}

		$query =
		"
			UPDATE sessions SET status = 'terminate'
			WHERE user_id = $_user_id AND  status = 'active'
			$where_code
		";
		\dash\db::query($query, \dash\db::get_db_log_name());

	}

	public static function terminate_all($_user_id)
	{
		$query =
		"
			UPDATE sessions SET status = 'terminate'
			WHERE user_id = $_user_id AND  status = 'active'
		";
		\dash\db::query($query, \dash\db::get_db_log_name());
	}



	/**
	 * inset new session in database
	 *
	 * @param      <type>  $_session  The session
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function set($_user_id)
	{
		$args =
		[
			'ip'       => \dash\server::ip(true),
			'agent_id' => \dash\agent::get(true),
			'user_id'  => $_user_id,
			'status'   => 'active'
		];

		$exist = self::get($args);

		$args['code']      = self::generate_code($_user_id);
		$args['last_seen'] = date("Y-m-d H:i:s");

		if(!$exist)
		{
			if(self::insert($args))
			{
				self::set_cookie($args['code']);
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			if(isset($exist['status']) && $exist['status'] === 'active')
			{
				if(isset($exist['code']))
				{
					if(self::login($exist['code']))
					{
						self::set_cookie($exist['code']);
					}
					else
					{
						return false;
					}
				}
				return true;
			}
			else
			{
				if(self::insert($args))
				{
					self::set_cookie($args['code']);
				}
				else
				{
					return false;
				}
				return true;
			}
		}
	}

	/**
	 * get the session details
	 *
	 * @param      <type>  $_session  The session
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get_active_sessions($_user_id, $_raw = false)
	{
		if(!$_user_id || !is_numeric($_user_id))
		{
			return false;
		}

		if($_raw)
		{
			$query = "SELECT * FROM  sessions WHERE `user_id` = '$_user_id' ORDER BY sessions.id DESC";
		}
		else
		{
			$query =
			"
				SELECT
					code,
					id,
					ip,
					last_seen,
					agent_id
				FROM
					sessions
				WHERE
					user_id = $_user_id AND
					status = 'active'
				ORDER BY sessions.id DESC
			";
		}

		$result = \dash\db::get($query, null, false, \dash\db::get_db_log_name());
		// get agent list form dash tools
		if($result && is_array($result))
		{
			$agent_id    = array_column($result, 'agent_id');
			$agent_id    = array_unique($agent_id);
			$agent_id    = implode(',', $agent_id);
			$agent_query = "SELECT * FROM agents WHERE id IN ($agent_id)";
			$agents      = \dash\db::get($agent_query, null, false, \dash\db::get_db_log_name());
			if($agents && is_array($agents))
			{
				$agent_id = array_column($agents, 'id');
				$agents   = array_combine($agent_id, $agents);
				foreach ($result as $key => $value)
				{
					if(isset($value['agent_id']))
					{
						if(array_key_exists($value['agent_id'], $agents))
						{
							// get agent group
							if(isset($agents[$value['agent_id']]['group']))
							{
								$result[$key]['agent_group'] = $agents[$value['agent_id']]['group'];
							}

							// get agent agent
							if(isset($agents[$value['agent_id']]['agent']))
							{
								$result[$key]['agent_agent'] = $agents[$value['agent_id']]['agent'];
							}

							// get agent name
							if(isset($agents[$value['agent_id']]['name']))
							{
								$result[$key]['agent_name'] = $agents[$value['agent_id']]['name'];
							}

							// get agent version
							if(isset($agents[$value['agent_id']]['version']))
							{
								$result[$key]['agent_version'] = $agents[$value['agent_id']]['version'];
							}

							// get agent os
							if(isset($agents[$value['agent_id']]['os']))
							{
								$result[$key]['agent_os'] = $agents[$value['agent_id']]['os'];
							}

							// get agent osnum
							if(isset($agents[$value['agent_id']]['osnum']))
							{
								$result[$key]['agent_osnum'] = $agents[$value['agent_id']]['osnum'];
							}
						}
					}
				}
			}
		}

		return $result;
	}

	/**
	 * get the session details
	 *
	 * @param      <type>  $_session  The session
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get_list($_user_id, $_raw = false)
	{
		if(!$_user_id || !is_numeric($_user_id))
		{
			return false;
		}

		if($_raw)
		{
			$query = "SELECT * FROM  sessions WHERE `user_id` = '$_user_id' ";
		}
		else
		{
			$query =
			"
				SELECT
					id,
					status,
					ip,
					last_seen,
					agent_id
				FROM
					sessions
				WHERE
					user_id = $_user_id
			";
		}

		$result = \dash\db::get($query, null, false, \dash\db::get_db_log_name());
		return $result;
	}


	/**
	 * the user logied by code
	 *
	 * @param      <type>  $_user_id  The user identifier
	 */
	public static function login($_code)
	{
		if($_code && is_string($_code))
		{
			return \dash\db::query("UPDATE sessions SET sessions.count = sessions.count + 1 WHERE code = '$_code'", \dash\db::get_db_log_name());
		}
	}


	/**
	 * change status
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_status   The status
	 */
	public static function change_status($_user_id, $_status, $_change_all_code = false)
	{
		if(!$_user_id || !is_numeric($_user_id) || !$_status || !is_string($_status))
		{
			return false;
		}

		$where_code = null;

		if(!$_change_all_code)
		{
			$code = self::get_cookie();
			if($code)
			{
				$where_code = " AND code = '$code' ";
			}
		}

		\dash\db::query("UPDATE sessions SET status = '$_status' WHERE user_id = $_user_id $where_code", \dash\db::get_db_log_name());

	}


	/**
	 * set status of code on logout
	 *
	 * @param      <type>  $_user_id  The user identifier
	 */
	public static function logout($_user_id)
	{
		self::change_status($_user_id, 'logout');
		self::terminate_cookie(false);
	}


	/**
	 * set status of code on changepass
	 *
	 * @param      <type>   $_user_id  The user identifier
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function change_password($_user_id)
	{
		$code = self::get_cookie();

		$where_code = null;

		if($code)
		{
			$where_code = " AND sessions.code != '$code' ";
		}

		\dash\db::query("UPDATE sessions SET status = 'changed' WHERE user_id = $_user_id $where_code", \dash\db::get_db_log_name());

	}

	/**
	 * set status of code on changepass
	 *
	 * @param      <type>   $_user_id  The user identifier
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function delete_account($_user_id)
	{
		self::change_status($_user_id, 'disable', true);
	}

}
?>
