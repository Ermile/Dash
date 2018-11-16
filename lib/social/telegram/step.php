<?php
namespace dash\social\telegram;

/** telegram step by step library**/
class step
{
	/**
	 * this library help create step by step messages
	 * v4.0
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


	public static function alive()
	{
		if(isset($_SESSION['tg']['step']['name']))
		{
			return true;
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
		// if before this message step started
		if(self::alive())
		{
			// its okay dont find answer because we are in step
			tg::ok();
			// clean $_text form botname
			if(strpos($_text, '@') !== false && strpos($_text, 'bot') !== false)
			{
				$_text = strtok($_text, '@');
			}

			$forceCancel = null;
			$currentStep = null;
			// calc current step
			switch ($_text)
			{
				case '/done':
				case '/end':
				case '/stop':
				case '/cancel':
				case T_('cancel'):
				case T_('Cancel'):
					// if user want to stop current step
					$currentStep = 'stop';
					$forceCancel = true;
					break;

				default:
					if(self::get('pointer'))
					{
						$currentStep = 'step'. self::get('pointer');
					}
					break;
			}

			if($currentStep)
			{
				$myhookLocation = '\content_hook\tg\\';
				// create function full name
				$funcName       = 'step_'. self::get('name'). '::'. $currentStep;
				if(self::get('name'))
				{
					$cmdNamespace = '\\'. __NAMESPACE__. '\commands\\';
					if(is_callable('\lib\tg\\'. $funcName))
					{
						// check in cuurent project
						call_user_func('\lib\tg\\'. $funcName, $_text);
					}
					elseif(is_callable($cmdNamespace. $funcName))
					{
						// get and return response
						call_user_func($cmdNamespace. $funcName, $_text);
					}
					else
					{
						$forceCancel = true;
					}
				}
				else
				{
					$forceCancel = true;
				}
			}
			else
			{
				$forceCancel = true;
			}

			// save text afrer reading current step function
			self::set('text', $_text);

			// if want to stop at the end call stop func
			if($forceCancel)
			{
				self::cancelStep();
			}
		}
	}


	public static function checkFalseTry($_text = null)
	{
		if($_text === 'reset')
		{
			self::set('falseTry', 0);
			return true;
		}

		if($_text === null)
		{
			$_text = hook::cmd('command');
		}

		if($_text === true)
		{
			// its okay, do nothing
		}
		else
		{
			switch ($_text)
			{
				case '/start':
				case '/lang':
				case '/language':
				case '/me':
				case '/whoami':
				case '/contact':
				case '/about':
				case '/help':
				case '/help':
				case '/ls':
				case '/?':
				case '/؟':
				case '/ticket':
				case '/register':
				case '/signup':
				case '/sync':
				case '/menu':
				case '/mainmenu':
				case '/return':
				case 'type_contact':
				case 'type_location':
				case 'type_audio':
				case 'type_document':
				case 'type_photo':
				case 'type_sticker':
				case 'type_video':
				case 'type_voice':
				case 'type_venue':

				case T_('start'):
				case T_('language'):
				case T_('about'):
				case T_('me'):
				case T_('contact'):
				case T_('address'):
				case T_('tel'):
				case T_('telephone'):
				case T_('mobile'):
				case T_('phone'):
				case T_('website'):
				case T_('email'):
				case T_('register'):
				case T_('signup'):
				case T_('sync'):
				case T_('help'):
				case T_('menu'):
				case T_('mainmenu'):
				case T_('return'):

				case 'ls':
				case '؟':
				case '?':
					// do nothing, this are common commands
					// if user press this commands say error message
					break;

				default:
					return false;
					break;
			}
		}


		// get current try val
		$tryCount = intval(self::get('falseTry'));
		// plus plus try val
		self::set('falseTry', $tryCount + 1);

		if($tryCount === 0)
		{
			$result =
			[
				'text'         => '⚠️ '. T_('Press enter valid value'),
				'reply_markup' =>
				[
					'keyboard' => [[T_('Cancel')]],
					'resize_keyboard' => true,
					'one_time_keyboard' => true
				],
			];
			tg::sendMessage($result);
			tg::ok();
		}
		else if($tryCount === 1)
		{
			$result =
			[
				'text'         => '⚠️⚠️ '.T_('Press another inappropirate key to exit from active process!'),
				'reply_markup' =>
				[
					'keyboard' => [[T_('Cancel')]],
					'resize_keyboard' => true,
					'one_time_keyboard' => true
				],
			];
			tg::sendMessage($result);
			tg::ok();
		}
		else
		{
			self::set('falseTry', 0);
			self::cancelStep();
		}
		return true;
	}


	public static function cancelStep()
	{
		self::stop();
		tg::sendMessage(T_('Cancel operation.'));
		tg::ok();
	}
}
?>