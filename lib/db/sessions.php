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
	private static function generate_code($_user_id)
	{
		$code =  'Ermile'. $_user_id. '_;)_'. time(). '(^_^)' . rand(1000, 9999);
		$code = \lib\utility::hasher($code, false);
		$code = \lib\safe::safe($code);
		return $code;
	}


	/**
	 * insert sessions on database
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	private static function insert($_args)
	{
		$set = \lib\db\config::make_set($_args);
		if(!trim($set))
		{
			return false;
		}

		return \lib\db::query("INSERT INTO sessions SET $set");
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
		return \lib\db::get($query, null, true);
	}


	/**
	 * get record is exist or no
	 *
	 * @param      <type>  $_args  The arguments
	 */
	private static function get($_args)
	{
		if(\lib\temp::get('db_remember_me_query'))
		{
			return \lib\temp::get('db_remember_me_query_result');
		}

		$where = \lib\db\config::make_where($_args);
		if(!trim($where))
		{
			return false;
		}

		$get   = \lib\db::get("SELECT * FROM sessions WHERE $where LIMIT 1", null, true);
		\lib\temp::set('db_remember_me_query', true);
		\lib\temp::set('db_remember_me_query_result', $get);
		return $get;
	}


	/**
	 * check_code session code
	 *
	 * @param      <type>  $_code  The code
	 */
	private static function check_code($_code)
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
		return \lib\utility\cookie::read('remember_me_');
	}


	/**
	 * Gets the user identifier.
	 *
	 * @return     <type>  The user identifier.
	 */
	public static function get_user_id()
	{
		$code = self::get_cookie();
		$get  = self::get(['code' => $code, 'status' => 'active']);

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

		\lib\db::query("UPDATE sessions SET status = 'terminate' WHERE id = $_id LIMIT 1");
	}


	/**
	 * Terminate the cookie.
	 *
	 * @param      <type>  $_code  The code
	 */
	private static function terminate_cookie()
	{
		\lib\utility\cookie::delete("remember_me_");
	}


	/**
	 * Sets the cookie.
	 *
	 * @param      <type>  $_code  The code
	 */
	private static function set_cookie($_code)
	{
		$cookie_domain = '.'. \lib\url::domain();
		setcookie("remember_me_", $_code, time() + (60*60*24*365), '/', $cookie_domain);
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
			'ip'       => \lib\server::ip(true),
			'agent_id' => \lib\agent::get(true),
			'user_id'  => $_user_id,
			'status'   => 'active'
		];

		$exist = self::get($args);

		$args['code']      = self::generate_code($_user_id);
		$args['last_seen'] = date("Y-m-d H:i:s");

		if(!$exist)
		{
			self::insert($args);
			self::set_cookie($args['code']);
			return true;
		}
		else
		{
			if(isset($exist['status']) && $exist['status'] === 'active')
			{
				if(isset($exist['code']))
				{
					self::login($exist['code']);
					self::set_cookie($exist['code']);
				}
				return true;
			}
			else
			{
				self::insert($args);
				self::set_cookie($args['code']);
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
			$query = "SELECT * FROM  sessions WHERE `user_id` = '$_user_id' ";
		}
		else
		{
			$query =
			"
				SELECT
					id,
					ip,
					last_seen,
					agent_id
				FROM
					sessions
				WHERE
					user_id = $_user_id AND
					status = 'active'
			";
		}

		$result = \lib\db::get($query, null);
		// get agent list form dash tools
		if($result && is_array($result))
		{
			$agent_id    = array_column($result, 'agent_id');
			$agent_id    = array_unique($agent_id);
			$agent_id    = implode(',', $agent_id);
			$agent_query = "SELECT * FROM agents WHERE id IN ($agent_id)";
			$agents      = \lib\db::get($agent_query);
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

		$result = \lib\db::get($query, null);
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
			\lib\db::query("UPDATE sessions SET sessions.count = sessions.count + 1 WHERE code = '$_code'");
		}
	}


	/**
	 * change status
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_status   The status
	 */
	private static function change_status($_user_id, $_status, $_change_all_code = false)
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

		\lib\db::query("UPDATE sessions SET status = '$_status' WHERE user_id = $_user_id $where_code");

	}


	/**
	 * set status of code on logout
	 *
	 * @param      <type>  $_user_id  The user identifier
	 */
	public static function logout($_user_id)
	{
		self::change_status($_user_id, 'logout');
		self::terminate_cookie();
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
		self::change_status($_user_id, 'changed', true);
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
