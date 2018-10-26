<?php
namespace dash\social\telegram;

class exec_before
{
	public static function check($_method, $_data)
	{
		if(!is_array($_data))
		{
			$_data = ['text' => $_data];
		}
		if(isset($_data['text']))
		{
			// if(strpos($_data['text'], '<!DOCTYPE html'))
			// {
			// 	\dash\notif::error('error');
			// }
			$_data['text'] = strip_tags($_data['text'], '<b><i><a><code><pre>');
		}

		// if chat id is not set then set it
		if(!isset($_data['chat_id']) && hook::chat())
		{
			// require chat id
			$_data['chat_id'] = hook::chat();
		}

		// add default menu if this message does not contain menu
		if(!isset($_data['reply_markup']))
		{
			$_data['reply_markup'] = commands\ermile::mainmenu(true);
		}

		// check needle of each type and try to add something to this method
		switch ($_method)
		{
			case 'sendMessage':
				// add parse_mode
				if(!isset($_data['parse_mode']))
				{
					// require chat id
					$_data['parse_mode'] = 'html';
				}

				// add reply message id
				if(isset($_data['reply_to_message_id']) && $_data['reply_to_message_id'] === true)
				{
					$_data['reply_to_message_id'] = hook::message_id();
					if(!$_data['reply_to_message_id'])
					{
						unset($_data['reply_to_message_id']);
					}
				}

				break;

			case 'answerCallbackQuery':
				// add callback query id
				if(hook::callback_query('id'))
				{
					$_data['callback_query_id'] = hook::callback_query('id');
				}
				break;

			case 'editMessageText':
			case 'editMessageCaption':
			case 'editMessageReplyMarkup':
				$_data['message_id'] = hook::message_id();
				break;

			case 'getUserProfilePhotos':
				$_data['user_id']    = hook::from();
				break;

			case 'sendPhoto':
			case 'sendAudio':
			case 'sendDocument':
			case 'sendVideo':
			case 'sendAnimation':
			case 'sendVoice':
			case 'sendVideoNote':
			case 'sendMediaGroup':
			case 'sendLocation':
			case 'sendVenue':
			case 'sendContact':
			case 'sendChatAction':
			default:
				// add parse_mode
				if(!isset($_data['parse_mode']))
				{
					// require chat id
					$_data['parse_mode'] = 'html';
				}
				break;
		}

		if(isset($_data['reply_markup']))
		{
			$_data['reply_markup'] = json_encode($_data['reply_markup'], JSON_UNESCAPED_UNICODE);
		}

		return $_data;
	}


	// /**
	//  * replace fill values if exist
	//  * @param  [type] $_data [description]
	//  * @return [type]        [description]
	//  */
	// public static function replaceFill($_data)
	// {
	// 	if(!tg::$fill)
	// 	{
	// 		return $_data;
	// 	}

	// 	// replace all texts
	// 	if(isset($_data['text']))
	// 	{
	// 		foreach (tg::$fill as $search => $replace)
	// 		{
	// 			$search	= '_'.$search.'_';
	// 			$_data['text'] = str_replace($search, $replace, $_data['text']);
	// 		}
	// 	}

	// 	// replace all texts
	// 	if(isset($_data['caption']))
	// 	{
	// 		foreach (tg::$fill as $search => $replace)
	// 		{
	// 			$search	= '_'.$search.'_';
	// 			$_data['caption'] = str_replace($search, $replace, $_data['caption']);
	// 		}
	// 	}

	// 	if(isset($_data['reply_markup']['keyboard']))
	// 	{
	// 		foreach ($_data['reply_markup']['keyboard'] as $itemRowKey => $itemRow)
	// 		{
	// 			foreach ($itemRow as $key => $itemValue)
	// 			{
	// 				if(!is_array($itemValue))
	// 				{
	// 					foreach (tg::$fill as $search => $replace)
	// 					{
	// 						$search	= '_'.$search.'_';
	// 						$newValue = str_replace($search, $replace, $itemValue);

	// 						$_data['reply_markup']['keyboard'][$itemRowKey][$key] = $newValue;
	// 					}
	// 				}
	// 			}
	// 		}
	// 	}
	// 	return $_data;
	// }
}
?>