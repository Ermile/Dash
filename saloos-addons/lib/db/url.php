<?php
namespace lib\db;

/** url managing **/
class url
{
	/**
	 * this library handle url
	 * v1.0
	 */


	/**
	 * [find_url_from_shortURL description]
	 * @param  [type] $_shortURL [description]
	 * @return [type]            [description]
	 */
	public static function checkShortURL($_shortURL = null)
	{
		// set this shorturl, real url:)
		if(!\lib\option::config('short_url'))
		{
			return null;
		}
		if(!$_shortURL)
		{
			$_shortURL = \lib\router::get_url();
		}
		$table     = null;
		$field     = null;
		$urlPrefix = substr($_shortURL, 0, 3);
		switch ($urlPrefix)
		{
			case 'sp_':
				// if this is url of one post
				$table = 'post';
				$field = "*";
				break;

			case 'st_':
				// else if this is url of one term
				$table = 'term';
				$field = 'term_url as url';
				break;
		}
		// if prefix is not correct return false
		if(!$table)
		{
			return null;
		}
		// remove prefix from url
		$_shortURL = substr($_shortURL, 3);
		$id        = \lib\utility\shortURL::decode($_shortURL);
		$table     .= 's';
		$qry       = "SELECT $field FROM $table WHERE id = $id";
		$result    = \lib\db::get($qry, null, true);
		if(!is_array($result))
		{
			return false;
		}

		if(!\lib\option::config('force_short_url') && isset($result['post_url']))
		{
			$post_url = $result['post_url'];
			// redirect to url of this post
			$myredirect = new \lib\redirector();
			$myredirect->set_url($post_url)->redirect();
		}
		// if not force simulate this url
		return $result;
	}
}
?>