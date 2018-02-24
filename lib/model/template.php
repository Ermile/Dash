<?php
namespace lib\model;

trait template
{
	/**
	 * this fuction check the url entered from user in database
	 * first search in posts and if not exist search in terms table
	 * @return [array] datarow of result if exist else return false
	 */
	public function s_template_finder()
	{
		//first of all search in url field if exist return row data
		$tmp_result = $this->get_posts();
		if($tmp_result)
		{
			return $tmp_result;
		}


		//else retun false
		return false;
	}


	/**
	 * [get_posts description]
	 * @param  boolean $_forcheck [description]
	 * @return [type]             [description]
	 */
	public function get_posts()
	{
		$url = $this->url('path');
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
			SELECT *
			FROM posts
			WHERE
			(
				posts.language IS NULL OR
				posts.language = '$language'
			)
			AND  posts.url = '$url'
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

		if($datarow && $id)
		{

			if(isset($datarow['type']) && isset($datarow['slug']))
			{
				// get cat from url until last slash
				$cat = substr($datarow['url'], 0, strrpos($datarow['url'], '/'));
				// if type of post exist in cat, remove it
				if($datarow['type'] === substr($cat, 0, strlen($datarow['type'])))
				{
					$cat = substr($cat, strlen($datarow['type'])+1);
				}

				$return =
				[
					'table' => 'posts',
					'type' => $datarow['type'],
					'cat'  => $cat,
					'slug' => $datarow['slug'],
				];
				return $return;
			}
			else
			{
				foreach ($datarow as $key => $value)
				{
					// if field contain json, decode it
					if(substr($value, 0, 1) == '{')
					{
						$datarow[$key] = json_decode($value, true);
						if(is_null($datarow[$key]) && preg_match("/meta$/", $key))
						{
							$datarow[$key] = json_decode(html_entity_decode($value), true);
						}
					}
				}

				// get meta of this post
				// $meta = \lib\db\posts::get_meta($id);
				// $datarow['postmeta'] = $meta;
				return $datarow;
			}
		}
		return false;
	}
}
?>
