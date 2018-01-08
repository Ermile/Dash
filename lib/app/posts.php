<?php
namespace lib\app;

class posts
{
	public static $datarow = null;


	public static function get_url()
	{
		$myUrl = \lib\router::get_url('_');
		$myUrl = \lib\router::urlfilterer($myUrl);
		return $myUrl;
	}


	public static function find_post()
	{
		$url = self::get_url();
		$url = str_replace("'", '', $url);
		$url = str_replace('"', '', $url);
		$url = str_replace('`', '', $url);
		$url = str_replace('%', '', $url);

		if(substr($url, 0, 7) == 'static/' || substr($url, 0, 6) == 'files/')
		{
			return false;
		}

		$language = \lib\define::get_language();
		$preview  = \lib\utility::get('preview');
		$qry =
		"
			SELECT
				*
			FROM
				posts
			WHERE
			(
				posts.language IS NULL OR
				posts.language = '$language'
			) AND
			posts.url = '$url'
			LIMIT 1
		";

		$datarow = \lib\db::get($qry, null, true);

		if(isset($datarow['user_id']) && (int) $datarow['user_id'] === (int) \lib\user::id())
		{
			// no problem to load this post
		}
		else
		{
			if($preview)
			{
				// no problem to load this post
			}
			else
			{
				if(isset($datarow['status']) && $datarow['status'] == 'publish')
				{
					// no problem to load this poll
				}
				else
				{
					$datarow = false;
				}
			}
		}

		// we have more than one record
		if(isset($datarow[0]))
		{
			$datarow = false;
		}

		if(isset($datarow['id']))
		{
			$id = $datarow['id'];
		}
		else
		{
			$datarow = false;
			$id  = 0;
		}

		self::$datarow = $datarow;

		return $datarow;
	}
}
?>