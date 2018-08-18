<?php
namespace dash\social\telegram;

class exec_before
{

	public static function check($_method, $_data)
	{
		// check needle of each type and try to add something to this method
		var_dump($_method);
		switch ($_method)
		{
			case 'sendMessage':
				// if chat id is not set then set it
				if(!isset($_data['chat_id']))
				{
					// require chat id
					$_data['chat_id'] = hook::chat();
				}
				// add parse_mode
				// if chat id is not set then set it
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

			default:

				break;
		}



		var_dump($_data);
		// exit();

		return $_data;
	}
}
?>