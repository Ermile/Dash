<?php
namespace lib\mvc\viewes;

trait posts
{
	/**
	 * [view_posts description]
	 * @return [type] [description]
	 */
	function view_posts()
	{
		$this->data->post = [];
		$tmp_result       = $this->model()->get_posts();
		$tmp_fields       =
		[
			'id'               =>'id',
			'post_language'    =>'language',
			'post_title'       =>'title',
			'post_slug'        =>'slug',
			'post_content'     =>'content',
			'post_meta'        =>'meta',
			'post_type'        =>'type',
			'post_url'         =>'url',
			'post_comment'     =>'comment',
			'post_count'       =>'count',
			'post_status'      =>'status',
			'post_parent'      =>'parent',
			'user_id'          =>'user',
			'post_publishdate' =>'publishdate',
			'date_modified'    =>'modified'
		];

		foreach ($tmp_fields as $key => $value)
		{
			if(is_array($tmp_result[$key]))
			{
				$this->data->post[$value] = $tmp_result[$key];
			}
			else
			{
				$this->data->post[$value] = html_entity_decode(trim(html_entity_decode($tmp_result[$key])));
			}
		}

		// set page title
		$this->data->page['title'] = $this->data->post['title'];
		$this->data->page['desc'] = \lib\utility\excerpt::extractRelevant($this->data->post['content'], $this->data->page['title']);
		$this->set_title();

		$this->data->nav = $this->model()->sp_nav();
	}
}
?>