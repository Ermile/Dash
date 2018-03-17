<?php
namespace addons\content_api\v1\ref\tools;


trait get
{
	public function get_ref_count($_args = [])
	{
		// default args
		$default_args =
		[
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
		if($_args['debug']) \lib\notif::title(T_("Operation Faild"));

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

		// check ref id is exist
		if(!$this->user_id)
		{
			if($_args['save_log']) \lib\db\logs::set('addon:api:ref:user_id:notfound', $this->user_id, $log_meta);
			if($_args['debug']) \lib\notif::error(T_("ref not found"), 'ref', 'permission');
			return false;
		}


		$result = [];

		$where_count_click =
		[
			'user_id' => $this->user_id,
			'cat'     => 'user_ref_'. (string) $this->user_id,
			'limit'   => 1,
		];
		$cout_click = \lib\db\options::get($where_count_click);

		if(isset($cout_click['value']))
		{
			$result['click'] = intval($cout_click['value']);
		}

		$result['signup'] = intval(\lib\db\users::get_ref_count(['ref' => $this->user_id]));

		return $result;
	}
}
?>