<?php
namespace addons\content_api\v1\term\tools;


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
		if($_args['debug']) \lib\debug::title(T_("Operation Faild"));

		// set the log meta
		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'user_id' => $this->user_id,
				'input'   => \lib\utility::request(),
			]
		];

		// check term id is exist
		if(!$this->user_id)
		{
			if($_args['save_log']) \lib\db\logs::set('addon:api:term:user_id:notfound', $this->user_id, $log_meta);
			if($_args['debug']) \lib\debug::error(T_("term not found"), 'term', 'permission');
			return false;
		}

		$duplicate = \lib\utility::isset_request('duplicate') ? \lib\utility::request('duplicate') ? true : false : null;

		/**
		 * check and set the args
		 */
		$return_function = $this->term_check_args($_args, $args, $log_meta);

		if(!\lib\debug::$status || $return_function === false)
		{
			return false;
		}

		if(\lib\debug::$status)
		{
			if($_args['debug']) \lib\debug::title(T_("Operation Complete"));

			if($_args['method'] === 'post')
			{
				if($_args['debug']) \lib\debug::true(T_("term successfully added"));
			}
			elseif($_args['method'] === 'patch')
			{
				if($_args['debug']) \lib\debug::true(T_("term successfully updated"));
			}
		}
	}
}
?>