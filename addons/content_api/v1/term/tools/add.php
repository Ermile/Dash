<?php
namespace addons\content_api\v1\term\tools;
use \lib\utility;
use \lib\debug;
use \lib\db\logs;

trait add
{
	public $apiTerm_cat = [];
	public $apiTerm_tag = [];

	use term_check_args;

	/**
	 * Adds a term.
	 *
	 * @param      array    $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function add_term($_args = [])
	{

		// ready to insert termteam or termbranch record
		$args                  = [];

		// default args
		$default_args =
		[
			'method'   => 'post',
			'debug'    => true,
			'save_log' => true,
		];

		if(!is_array($_args))
		{
			$_args = [];
		}
		// merge default args and args
		$_args = array_merge($default_args, $_args);

		// set default title of debug
		if($_args['debug']) debug::title(T_("Operation Faild"));

		// set the log meta
		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'user_id' => $this->user_id,
				'input'   => utility::request(),
			]
		];

		// check term id is exist
		if(!$this->user_id)
		{
			if($_args['save_log']) logs::set('addon:api:term:user_id:notfound', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("term not found"), 'term', 'permission');
			return false;
		}

		$duplicate = utility::isset_request('duplicate') ? utility::request('duplicate') ? true : false : null;

		/**
		 * check and set the args
		 */
		$return_function = $this->term_check_args($_args, $args, $log_meta);

		if(!debug::$status || $return_function === false)
		{
			return false;
		}

		if(debug::$status)
		{
			if($_args['debug']) debug::title(T_("Operation Complete"));

			if($_args['method'] === 'post')
			{
				if($_args['debug']) debug::true(T_("term successfully added"));
			}
			elseif($_args['method'] === 'patch')
			{
				if($_args['debug']) debug::true(T_("term successfully updated"));
			}
		}
	}
}
?>