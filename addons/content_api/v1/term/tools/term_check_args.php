<?php
namespace addons\content_api\v1\term\tools;


trait term_check_args
{
	public function term_check_args($_args, &$args, $_log_meta, $_type = 'insert')
	{
		$log_meta = $_log_meta;

		// term detail
		$language = \lib\utility::request('language');
		$language = trim($language);
		if($language && !\lib\language::check($language))
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:term:language:invalid', $this->user_id, $log_meta);
			if($_args['debug']) \lib\notif::error(T_("Invalid parameter language"), 'language', 'arguments');
			return false;
		}

		// $title = \lib\utility::request('title');
		// $title = trim($title);
		// if($title && mb_strlen($title) > 50)
		// {
		// 	if($_args['save_log']) \lib\db\logs::set('addons:api:term:title:max:lenth', $this->user_id, $log_meta);
		// 	if($_args['debug']) \lib\notif::error(T_("Invalid parameter title"), 'title', 'arguments');
		// 	return false;
		// }

		// $slug = \lib\utility::request('slug');
		// $slug = trim($slug);
		// if($slug && mb_strlen($slug) > 50)
		// {
		// 	if($_args['save_log']) \lib\db\logs::set('addons:api:term:slug:max:lenth', $this->user_id, $log_meta);
		// 	if($_args['debug']) \lib\notif::error(T_("Invalid parameter slug"), 'slug', 'arguments');
		// 	return false;
		// }

		// if(!$slug && $title)
		// {
		// 	$slug = \lib\utility\filter::slug($title);
		// }

		// if(!$slug)
		// {
		// 	if($_args['save_log']) \lib\db\logs::set('addons:api:term:slug:can:not:null', $this->user_id, $log_meta);
		// 	if($_args['debug']) \lib\notif::error(T_("Title or slug is required"), 'slug', 'arguments');
		// 	return false;
		// }

		// $url = \lib\utility::request('url');
		// $url = trim($url);
		// if($url && mb_strlen($url) > 50)
		// {
		// 	if($_args['save_log']) \lib\db\logs::set('addons:api:term:url:max:lenth', $this->user_id, $log_meta);
		// 	if($_args['debug']) \lib\notif::error(T_("Invalid parameter url"), 'url', 'arguments');
		// 	return false;
		// }

		// $desc = \lib\utility::request('desc');
		// $desc = trim($desc);
		// if($desc && mb_strlen($desc) > 50)
		// {
		// 	if($_args['save_log']) \lib\db\logs::set('addons:api:term:desc:max:lenth', $this->user_id, $log_meta);
		// 	if($_args['debug']) \lib\notif::error(T_("Invalid parameter desc"), 'desc', 'arguments');
		// 	return false;
		// }


		$related = \lib\utility::request('related');
		$related = trim($related);
		if(!$related)
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:term:related:not:set', $this->user_id, $log_meta);
			if($_args['debug']) \lib\notif::error(T_("Related parameter not set"), 'related', 'arguments');
			return false;
		}

		$default_related = ['posts','products','attachments','files','comments','users'];

		if(defined('termusage_related') && is_array(termusage_related))
		{
			$default_related = termusage_related;
		}

		if(!in_array($related, $default_related))
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:term:related:invalid', $this->user_id, $log_meta);
			if($_args['debug']) \lib\notif::error(T_("Invalid related parameter"), 'related', 'arguments');
			return false;
		}

		$related_id = \lib\utility::request('related_id');
		if(!$related_id)
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:term:related_id:invalid', $this->user_id, $log_meta);
			if($_args['debug']) \lib\notif::error(T_("Invalid related_id parameter"), 'related_id', 'arguments');
			return false;
		}

		if(!is_array($related_id))
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:term:related_id:invalid:array', $this->user_id, $log_meta);
			if($_args['debug']) \lib\notif::error(T_("Related id must be array"), 'related_id', 'arguments');
			return false;
		}

		$related_ids = [];
		foreach ($related_id as $key => $value)
		{
			$temp = \lib\coding::decode($value);
			if($temp)
			{
				array_push($related_ids, $temp);
			}
		}

		if(empty($related_id))
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:term:related_id:invalid:array:empty', $this->user_id, $log_meta);
			if($_args['debug']) \lib\notif::error(T_("No valid related_id found"), 'related_id', 'arguments');
			return false;
		}

		$order = \lib\utility::request('order');
		$order = trim($order);
		if($order && !is_numeric($order))
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:term:order:invalid', $this->user_id, $log_meta);
			if($_args['debug']) \lib\notif::error(T_("Invalid order parameter"), 'order', 'arguments');
			return false;
		}

		if($order && intval($order) > 9999)
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:term:order:invalid:max', $this->user_id, $log_meta);
			if($_args['debug']) \lib\notif::error(T_("You must set the order less than 9999"), 'order', 'arguments');
			return false;
		}

		$default_status = ['enable','disable','expired','awaiting','filtered','blocked','spam','violence','pornography','other','deleted'];

		$status = \lib\utility::request('status');
		$status = trim($status);
		if($status && !in_array($status, $default_status))
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:term:status:invalid', $this->user_id, $log_meta);
			if($_args['debug']) \lib\notif::error(T_("Invalid parameter status"), 'status', 'arguments');
			return false;
		}

		$type = \lib\utility::request('type');
		$type = trim($type);

		$check_type =
		[
			'cat',
			'tag',
			'term',
			'code',
			'other',
			'barcode1',
			'barcode2',
			'barcode3',
			'qrcode1',
			'qrcode2',
			'qrcode3',
			'rfid1',
			'rfid2',
			'rfid3',
			'fingerprint1',
			'fingerprint2',
			'fingerprint3',
			'fingerprint4',
			'fingerprint5',
			'fingerprint6',
			'fingerprint7',
			'fingerprint8',
			'fingerprint9',
			'fingerprint10'
		];

		if(defined('termusage_type') && is_array(termusage_type))
		{
			$check_type = termusage_type;
		}

		if($type && !in_array($type, $check_type))
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:term:type:invalid', $this->user_id, $log_meta);
			if($_args['debug']) \lib\notif::error(T_("Invalid parameter type"), 'type', 'arguments');
			return false;
		}

		// term usage detail
		$cat = \lib\utility::request('cat');
		if($cat && !is_array($cat))
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:term:cat:not:array', $this->user_id, $log_meta);
			if($_args['debug']) \lib\notif::error(T_("Cats must be array"), 'cat', 'arguments');
			return false;
		}

		$tag = \lib\utility::request('tag');
		if($tag && !is_array($tag))
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:term:tag:not:array', $this->user_id, $log_meta);
			if($_args['debug']) \lib\notif::error(T_("Cats must be array"), 'tag', 'arguments');
			return false;
		}

		$term_type = null;
		switch ($type)
		{
			case 'cat':
			case 'tag':
			case 'term':
			case 'other':
				$term_type = $type;
				break;

			default:
				$term_type = 'other';
				break;
		}

		$duplicate = \lib\utility::request('duplicate') ? true : false;

		if($cat && is_array($cat))
		{
			foreach ($cat as $key => $value)
			{
				$value = trim($value);
				if(!$value)	continue;
				if(!is_string($value) && !is_numeric($value)) continue;


				$insert_term =
				[
					'type'  => $term_type,
					'title' => $value,
					'slug'  => \lib\utility\filter::slug($value),
				];

				$check_exist_term = \lib\db\terms::get(array_merge($insert_term, ['limit' => 1]));
				if(isset($check_exist_term['id']))
				{
					$term_id = $check_exist_term['id'];
				}
				else
				{
					$insert_term['user_id'] = $this->user_id;
					$term_id = \lib\db\terms::insert($insert_term);
				}



				foreach ($related_ids as $key => $value)
				{
					$insert_termusage =
					[
						'related'    => $related,
						'related_id' => $value,
						'type'       => $type,
					];

					if(!$duplicate)
					{
						\lib\db\termusages::hard_delete($insert_termusage);
					}

					$insert_termusage['term_id'] = $term_id;

					\lib\db\termusages::insert($insert_termusage);
				}
			}
		}
	}



	/**
	 * check args and make where
	 *
	 * @param      <type>  $_args      The arguments
	 * @param      <type>  $where      The where
	 * @param      <type>  $_log_meta  The log meta
	 */
	public function term_make_where($_args, &$where, $_log_meta)
	{
		$type = \lib\utility::request('type');
		$type = trim($type);
		if($type && is_string($type) || is_numeric($type))
		{
			$where['type'] = $type;
		}

		if(!$type && \lib\utility::isset_request('type'))
		{
			$where['type'] = null;
		}
	}
}
?>