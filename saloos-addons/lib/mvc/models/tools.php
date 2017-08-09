<?php
namespace lib\mvc\models;

trait tools
{
	/**
	 * [get_feeds description]
	 * @param  boolean $_forcheck [description]
	 * @return [type]             [description]
	 */
	public function get_feeds($_forcheck = false)
	{
		$start    = \lib\utility::get('start');
		$lenght   = \lib\utility::get('lenght');
		// search in url field if exist return row data
		$qry = $this->sql()->table('posts')
				->field(
					'#post_language as `lang`',
					'#post_title as `title`',
					'#post_content as `desc`',
					'#post_url as `link`',
					'#post_publishdate as `date`'
					)
				->where('post_type', 'post')
				->and('post_status', 'publish')
				->limit(0, 10);

		$qry = $qry->groupOpen('g_language');
		$qry = $qry->and('post_language', \lib\define::get_language());
		$qry = $qry->or('post_language', 'IS', 'NULL');
		$qry = $qry->groupClose('g_language');
		$qry = $qry->select();

		return $qry->allassoc();
	}
}
?>
