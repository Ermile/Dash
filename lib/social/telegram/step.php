<?php
namespace dash\social\telegram;

/** telegram step by step library**/
class step
{
	/**
	 * this library help create step by step messages
	 * v3.7
	 */

	/**
	 * define variables
	 * @param  [type] $_name name of current step for call specefic file
	 * @return [type]        [description]
	 */
	public static function start($_name)
	{
		// name of step for call specefic file
		self::set('name', $_name);
		// counter of step number, increase automatically
		self::set('counter', 1);
		// pointer of current step, can change by user commands
		self::set('pointer', 1);
		// save text of each step
		self::set('text', []);
		// save last entered text
		self::set('last', null);
		// save text status
		self::set('saveText', true);
		// save title for some text on saving
		self::set('textTitle', null);
	}


	/**
	 * delete session step value
	 * @return [type] [description]
	 */
	public static function stop()
	{
		unset($_SESSION['tg']['step']);
	}


	/**
	 * set specefic key of step
	 * @param  string $_key   name of key
	 * @param  string $_value value of this key
	 * @return [type]         [description]
	 */
	public static function set($_key, $_value)
	{
		// some condition for specefic keys
		switch ($_key)
		{
			case 'text':
				if(!is_string($_value))
				{
					return false;
				}
				// if savetext is off
				// turn it on and return
				if(!self::get('saveText'))
				{
					$_SESSION['tg']['step']['saveText'] = true;
					return null;
				}
				// if title of text isset use this title
				if($text_title = self::get('textTitle'))
				{
					$_SESSION['tg']['step'][$_key][$text_title] = $_value;
					// empty textTitle
					$_SESSION['tg']['step']['textTitle'] = null;
				}
				// else only add new text
				else
				{
					$_SESSION['tg']['step'][$_key][] = $_value;
				}
				$_SESSION['tg']['step']['last']    = $_value;
				$increase = 1;
				if(isset($_SESSION['tg']['step']['counter']))
				{
					$increase += $_SESSION['tg']['step']['counter'];
				}
				$_SESSION['tg']['step']['counter'] = $increase;
				break;

			case 'pointer':
				$_SESSION['tg']['step']['counter'] = $_SESSION['tg']['step']['counter'] + $_value;

			default:
				$_SESSION['tg']['step'][$_key] = $_value;
				// return that value was set!
				break;
		}
		// return true because it's okay!
		return true;
	}


	/**
	 * get specefic key of step
	 * @param  string $_key [description]
	 * @return [type]       [description]
	 */
	public static function get($_key = null)
	{
		if($_key === null)
		{
			if(isset($_SESSION['tg']['step']))
			{
				return $_SESSION['tg']['step'];
			}
		}
		elseif($_key === false)
		{
			if(isset($_SESSION['tg']['step']))
			{
				return true;
			}
		}
		elseif(isset($_SESSION['tg']['step'][$_key]))
		{
			return $_SESSION['tg']['step'][$_key];
		}
		elseif(isset($_SESSION['tg']['step']))
		{
			return null;
		}

		return false;
	}


	/**
	 * go to next step
	 * @param  integer  $_num number of jumping
	 * @return function       result of jump
	 */
	public static function plus($_num = 1, $_key = 'pointer', $_relative = true)
	{
		if($_relative)
		{
			$_num = self::get($_key) + $_num;
		}

		return self::set($_key, $_num);
	}


	public static function current()
	{
		if(self::get('name'))
		{
			return self::get('name'). '::step'. self::get('pointer');
		}
	}

	/**
	 * goto specefic step directly
	 * @param  integer $_step [description]
	 * @param  string  $_key  [description]
	 * @return [type]         result of jump
	 */
	public static function goingto($_step = 1, $_key = 'pointer')
	{
		return self::set($_key, $_step);
	}



	/**
	 * [check description]
	 * @param  [type] $_text [description]
	 * @return [type]        [description]
	 */
	public static function check($_text)
	{
		// $tmp_text =
		// "user_id_: ".   tg::$user_id.
		// "\n id: ".      session_id().
		// "\n name: ".    session_name().
		// "\n session: ". json_encode($_SESSION);
		// // for debug
		// $tmp =
		// [
		// 	'text' => $tmp_text
		// ];
		// $a = tg::sendMessage($tmp);
		// $a = self::sendMessage(['text' => json_encode($_SESSION['tg'], JSON_UNESCAPED_UNICODE)]);

		// if before this message step started
		if(self::get(false))
		{
			$forceCancel = null;
			// calc current step
			switch ($_text)
			{
				case '/done':
				case '/end':
				case '/stop':
				case '/cancel':
				case T_('cancel'):
					// if user want to stop current step
					$currentStep = 'stop';
					$forceCancel = true;
					break;

				default:
					$currentStep = 'step'. self::get('pointer');
					break;
			}

			$myhookLocation = '\content_hook\tg\\';
			// create function full name
			$funcName       = 'step_'. self::get('name'). '::'. $currentStep;
			// generate func name
			if(is_callable($myhookLocation.$funcName))
			{
				// get and return response
				call_user_func($myhookLocation.$funcName, $_text);
			}
			elseif(self::get('name'))
			{
				$cmdNamespace = '\\'. __NAMESPACE__. '\commands\\';
				if(is_callable($cmdNamespace.$funcName))
				{
					// get and return response
					call_user_func($cmdNamespace.$funcName, $_text);
				}
			}

			// save text afrer reading current step function
			self::set('text', $_text);
			// if want to stop at the end call stop func
			if($currentStep === 'stop')
			{
				self::stop();
			}
			if($forceCancel)
			{
				self::cancelStep();
			}
		}
	}


	public static function checkFalseTry()
	{
		// get current try val
		$tryCount = intval(self::get('falseTry'));
		// plus plus try val
		self::set('falseTry', $tryCount + 1);

		if($tryCount === 0)
		{
			tg::sendMessage(T_('Press enter valid value'));
			tg::ok();
		}
		else if($tryCount === 1)
		{
			tg::sendMessage(T_('Press another inappropirate key to exit from active process!'));
			tg::ok();
		}
		else
		{
			self::set('falseTry', 0);
			self::cancelStep();
		}
	}

	public static function cancelStep()
	{
		self::stop();
		tg::sendMessage(T_('Cancel operation.'));
		tg::ok();
	}
}
?>