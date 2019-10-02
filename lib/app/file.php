<?php
namespace dash\app;


class file
{
	public static $sort_field =
	[
		'id',
		'user_id',
		'md5',
		'filename',
		'title',
		'desc',
		'useage',
		'type',
		'mime',
		'ext',
		'folder',
		'url',
		'path',
		'size',
		'status',
		'datecreated',
		'datemodified',
	];

	public static function get_inline($_id)
	{
		$id = $_id;
		$id = \dash\coding::decode($id);
		if(!$id)
		{
			\dash\notif::error(T_("Id not set"));
			return false;
		}

		$result = \dash\db\files::get(['id' => $id, 'limit' => 1]);

		if(!$result)
		{
			\dash\notif::error(T_("Invalid id"));
			return false;
		}

		return $result;
	}


	public static function get($_id)
	{
		$result = self::get_inline($_get);

		if($result)
		{
			$result = self::ready($result);
		}

		return $result;
	}


	public static function list($_string = null, $_args = [])
	{

		if(!\dash\user::id())
		{
			return false;
		}

		$default_args =
		[
			'order' => null,
			'sort'  => null,
		];

		if(!is_array($_args))
		{
			$_args = [];
		}

		$option = [];
		$option = array_merge($default_args, $_args);

		if($option['order'])
		{
			if(!in_array($option['order'], ['asc', 'desc']))
			{
				unset($option['order']);
			}
		}

		if($option['sort'])
		{
			if(!in_array($option['sort'], self::$sort_field))
			{
				unset($option['sort']);
			}
		}

		$field             = [];

		$result = \dash\db\files::search($_string, $option, $field);

		$temp            = [];


		foreach ($result as $key => $value)
		{
			$check = self::ready($value);
			if($check)
			{
				$temp[] = $check;
			}
		}

		return $temp;
	}



	/**
	 * ready data of user to load in api
	 *
	 * @param      <type>  $_data  The data
	 */
	public static function ready($_data)
	{
		$result = [];

		foreach ($_data as $key => $value)
		{

			switch ($key)
			{
				case 'id':
				case 'user_id':
					if(isset($value))
					{
						$result[$key] = \dash\coding::encode($value);
					}
					else
					{
						$result[$key] = null;
					}
					break;



				default:
					$result[$key] = $value;
					break;
			}
		}

		return $result;
	}


	public static function upload_quick($_upload_name)
	{
		if(\dash\request::files($_upload_name))
		{
			$uploaded_file = self::upload(['debug' => false, 'upload_name' => $_upload_name]);

			if(isset($uploaded_file['url']))
			{
				return $uploaded_file['url'];
			}
			// if in upload have error return
			if(!\dash\engine\process::status())
			{
				return false;
			}
		}
		return null;
	}


	public static function upload($_options = [])
	{
		\dash\app::variable($_options);

		$default_options =
		[
			'upload_name' => \dash\app::request('upload_name'),
			'url'         => null,
			'debug'       => true,
			'max_upload'  => null,
		];

		if(!is_array($_options))
		{
			$_options = [];
		}

		$_options = array_merge($default_options, $_options);

		if(\dash\app::request('url') && !$_options['url'])
		{
			$_options['url'] = \dash\app::request('url');
		}

		$file_path = false;

		if($_options['url'])
		{
			$file_path = true;
		}
		elseif(!\dash\request::files($_options['upload_name']))
		{
			\dash\notif::error(T_("Unable to upload, because of selected upload name"), 'upload_name', 'arguments');
			return false;
		}

		$ready_upload               = [];
		$ready_upload['user_id']    = \dash\user::id();
		$ready_upload['debug']      = $_options['debug'];
		$ready_upload['max_upload'] = $_options['max_upload'];

		if($file_path)
		{
			$ready_upload['file_path'] = $_options['url'];
		}
		else
		{
			$ready_upload['upload_name'] = $_options['upload_name'];
		}

		$ready_upload['status'] = null;

		$upload      = \dash\utility\upload::upload($ready_upload);

		if(!\dash\engine\process::status())
		{
			return false;
		}

		$file_id     = null;

		if(isset($upload['id']) && is_numeric($upload['id']))
		{
			$file_id = $upload['id'];
		}
		else
		{
			\dash\notif::error(T_("Can not upload file. undefined error"));
			return false;
		}

		$file_id_code = null;

		if($file_id)
		{
			$file_id_code = \dash\coding::encode($file_id);
		}

		$url = null;

		\dash\log::set('uploadFile', ['code' => $file_id_code, 'datalink' => $url]);

		$result = array_merge($upload, ['code' => $file_id_code]);

		return $result;
	}
}

?>