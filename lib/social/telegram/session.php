<?php
namespace dash\social\telegram;

class session
{
	public static function forceSet()
	{

		$newName = \dash\url::root().'Telegram'. hook::from('id');
		self::restart($newName);
	}


	/**
	 * restart session with new session id
	 * @param  [type] $_session_id new session id
	 * @return [type]              [description]
	 */
	private static function restart($_session_id)
	{
		// if a session is currently opened, close it
		if (session_id() != '')
		{
			session_write_close();
		}
		// use new id
		session_id($_session_id);
		// start new session
		session_start();
	}
}
?>