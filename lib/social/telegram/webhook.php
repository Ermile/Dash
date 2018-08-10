<?php
namespace dash\social\telegram;

/** telegram **/
class webhook
{
	/**
	 * setWebhook for telegram
	 * @param string $_url  [description]
	 * @param [type] $_file [description]
	 */
	public static function set($_url = null, $_connections = null)
	{
		if(!$_url)
		{
			$_url = \dash\option::social('telegram', 'hook');
		}
		$property = ['url' => $_url];
		if($_connections)
		{
			$property['max_connections'] = $_connections;
		}

		return tg::setWebhook($property);
	}
}
?>