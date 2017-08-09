<?php
namespace addons\content_cp\home;

use \lib\utility;
use \lib\debug;
class model extends \mvc\model
{
	// ---------------------------------------------------- handle all type of request used for all common modules
	function get_delete()
	{
		$this->delete();
	}

	function delete_delete()
	{
		// var_dump(0);exit();
		$this->delete();
	}

	function post_delete()
	{
		$this->delete();
	}

	function post_add()
	{
		$this->insert();
	}

	function put_edit()
	{
		$this->update();
	}

	function post_options()
	{
		return 'soon';
	}

	/**
	 * -------------------------------------------------------- our custom code for this module is below this line

	 */

	public function countOf($_type)
	{
		$qry = $this->sql();
		switch ($_type)
		{
			case 'posts':
				$qry = \lib\db::get("SELECT COUNT(*) AS `count` FROM posts WHERE post_type = 'posts' ", 'count', true);
				// $qry = $qry->table('posts')->where('post_type', 'post');
				break;

			case 'pages':
				$qry = \lib\db::get("SELECT COUNT(*) AS `count` FROM posts WHERE post_type = 'page' ", 'count', true);
				// $qry = $qry->table('posts')->where('post_type', 'page');
				break;

			case 'helps':
				$qry = \lib\db::get("SELECT COUNT(*) AS `count` FROM posts WHERE post_type = 'help' ", 'count', true);
				// $qry = $qry->table('posts')->where('post_type', 'help');
				break;

			case 'attachments':
				$qry = \lib\db::get("SELECT COUNT(*) AS `count` FROM posts WHERE post_type = 'attachment' ", 'count', true);
				// $qry = $qry->table('posts')->where('post_type', 'attachment');
				break;

			case 'tags':
				$qry = \lib\db::get("SELECT COUNT(*) AS `count` FROM terms WHERE term_type = 'tag' ", 'count', true);
				// $qry = $qry->table('terms')->where('term_type', 'tag');
				break;

			case 'categories':
				$qry = \lib\db::get("SELECT COUNT(*) AS `count` FROM terms WHERE term_type = 'cat' ", 'count', true);
				// $qry = $qry->table('terms')->where('term_type', 'cat');
				break;

			case 'books':
				$qry = \lib\db::get("SELECT COUNT(*) AS `count` FROM posts WHERE post_type = 'book' ", 'count', true);
				// $qry = $qry->table('posts')->where('post_type', 'book');
				break;

			default:
				$qry = \lib\db::get("SELECT COUNT(*) AS `count` FROM `$_type` ", 'count', true);
				// $qry = $qry->table($_type);
				break;
		}

		// $count = $qry->select()->num();
		// $count = $count? $count: 0;
		$count  = $qry ? (int) $qry : 0;
		return $count;
	}

	public function visitors($_unique = false, $_period = 'DAY' )
	{
		$qry    = $this->sql()->table('visitors');

		if($_unique)
		{
			$qry    = $qry->groupbyVisitor_createdate('#date');
			$qry    = $qry->groupbyVisitor_ip('#ip');
			$qry    = $qry->field(
						"#date_format(visitor_createdate,'%Y-%m-%d') as date",
						"#SUM(CASE visitor_robot WHEN 'yes' THEN 1 ELSE 0 END) AS `bots`",
						"#SUM(CASE visitor_robot WHEN 'no' THEN 1 ELSE 0 END) AS `humans`",
						'#count(*) as value');
			// var_dump( $qry->orderVisitor_createdate('DESC')->limit(10)->selectString());
		}
		else
		{
			$qry    = $qry->groupbyVisitor_createdate('#date');
			$qry    = $qry->field(
						"#date_format(visitor_createdate,'%Y-%m-%d') as date",
						"#SUM(CASE visitor_robot WHEN 'yes' THEN 1 ELSE 0 END) AS `bots`",
						"#SUM(CASE visitor_robot WHEN 'no' THEN 1 ELSE 0 END) AS `humans`",
						// "#SUM(IF(user_id IS NULL, 0,1) ) AS users",
						'#count(*) as value');
		}

		$qry    = $qry->orderVisitor_createdate('DESC')->limit(10)->select();
		$result = $qry->allassoc();
		$result = array_reverse($result);

		return $result;
	}

	public function visitors_toppages($_count = 10 )
	{
		$qry    = $this->sql()->table('visitors');
		$qry    = $qry->field(
						'#visitor_url as url',
						'#count(*) as count');
		$qry    = $qry->where('visitor_robot', 'no');
		$qry    = $qry->and('user_id', 'is', 'NULL');
		$qry    = $qry->groupby('visitor_url');
		$qry    = $qry->order('#count','DESC')->limit($_count)->select();
		$result = $qry->allassoc();

		foreach ($result as $key => $row)
		{
			$result[$key]['url'] = urldecode($row['url']);

		}
		return $result;
	}

	public function sitemap($_table = 'posts', $_type = null)
	{
		$prefix = substr($_table, 0, -1);
		$status = $_table === 'posts'? 'publish': 'enable';
		$date   = $_table === 'posts'? 'post_publishdate': 'date_modified';
		$qry    = $this->sql()->table($_table)->where($prefix.'_status', $status);
		if($_type)
		{
			$qry = $qry->and($prefix.'_type', $_type);
		}
		elseif($_type === false && $_table === 'posts')
		{
			$qry = $qry->and($prefix.'_type', '<>', "'post'");
			$qry = $qry->and($prefix.'_type', '<>', "'page'");
			$qry = $qry->and($prefix.'_type', '<>', "'help'");
			$qry = $qry->and($prefix.'_type', '<>', "'attachments'");
		}


		$qry    = $qry->field($prefix.'_url', $date)->order('id','DESC');
		return $qry->select()->allassoc();
	}


	/**
	 * get list of datatable
	 * @return [type] return json contain datatable values
	 */
	public function get_list()
	{
		$datatable = $this->model()->datatable();

		// echo(json_encode($datatable, JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE));

		debug::property('draw'           , $datatable['draw']);
		debug::property('data'           , $datatable['data']);
		// debug::property('columns'        , $datatable['columns']);
		debug::property('recordsTotal'   , $datatable['total']);
		debug::property('recordsFiltered', $datatable['filter']);
		$this->model()->_processor(object(array("force_json" => true, "force_stop" => true)));
		// echo(json_encode($result, JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE));
		// exit();
	}





	/**
	 * set options
	 * @return [type] return json contain datatable values
	 */
	public function get_options()
	{
		$opt = $this->model()->options();
		debug::property('data'           , $opt);

		$this->model()->_processor(object(array("force_json" => true, "force_stop" => true)));
		// echo(json_encode($result, JSON_FORCE_OBJECT));
		// exit();
	}


	// this function get table name and return all record of it. table name can set in view
	// if user don't pass table name function use current real method name get from url
	public function datatable($_table = null, $_condition = null, $_rename = false)
	{
		$param_search = \lib\utility::get('search');

		$cpModule     = $this->cpModule();
		$mytype       = null;


		// set columns
		// get all fields of table and filter fields name for show in datatable, access from columns variable
		switch ($cpModule['raw'])
		{
			case 'categories':
			case 'pollcategories':
			case 'filecategories':
			case 'bookcategories':
			case 'helpcategories':
			case 'tags':
				$tmp_columns      = \lib\sql\getTable::get('terms');
				unset($tmp_columns['term_type'] );
				unset($tmp_columns['term_slug'] );
				break;

			case 'posts':
			case 'polls':
			case 'helps':
			case 'pages':
			case 'books':
			case 'twitter':
			case 'facebook':
			case 'telegram':
				$tmp_columns      = \lib\sql\getTable::get('posts');
				$tmp_columns['post_publishdate']['table'] = true;
				unset($tmp_columns['post_type'] );
				unset($tmp_columns['post_slug'] );
				unset($tmp_columns['user_id'] );
				break;

			case 'attachments':
				$tmp_columns      = \lib\sql\getTable::get('posts');
				$tmp_columns['post_meta']['table'] = true;
				unset($tmp_columns['post_type'] );
				unset($tmp_columns['post_slug'] );
				unset($tmp_columns['post_status'] );
				unset($tmp_columns['user_id'] );
				// add type column
				$tmp_columns['post_meta']['label'] = T_('type');
				$tmp_columns['post_meta']['value'] = 'filetype';
				break;

			case 'users':
				$tmp_columns      = \lib\sql\getTable::get('users');
				unset($tmp_columns['user_pass'] );
				$tmp_columns['user_email']['table'] = true;
				$tmp_columns['user_displayname']['table'] = true;
				$tmp_columns['user_status']['table'] = true;
				break;

			default:
				$tmp_columns      = \lib\sql\getTable::get($cpModule['table']);
				break;
		}


		if (!$_table)
			$_table = $cpModule['table'];
		$qry          = $this->sql()->table($_table);

		switch ($cpModule['raw'])
		{
			case 'categories':
			case 'filecategories':
			case 'pollcategories':
			case 'bookcategories':
			case 'helpcategories':
			case 'tags':
			case 'books':
			case 'posts':
			case 'polls':
			case 'pages':
			case 'helps':
			case 'attachments':
			case 'socialnetwork':
				$mytype = [$cpModule['prefix'].'_type' => $cpModule['type']];
				break;

			case 'profile':
				// $this->data->datarow = $this->model()->datarow('users', $this->login('id'));
				break;

			default:
				$mytype = null;
				break;
		}
		if($cpModule['prefix'] === 'post' || $cpModule['prefix'] === 'term')
		{
			$lang = \lib\define::get_language('name');
			$qry  = $qry->and($cpModule['prefix'].'_language', $lang);
		}

		if(is_array($mytype))
		{
			foreach ($mytype as $key => $value)
			{
				$qry = $qry->and($key, 'LIKE', "'$value%'");
			}
		}
		$total = $qry->select()->num();

		if(is_array($_condition))
		{
			foreach ($_condition as $key => $value)
				$qry = $qry->and($key, $value);
		}
		$param_draw   = \lib\utility::get('draw');
		if(!$param_draw)
		{
			$param_draw = 1;
		}

		if($param_search)
		{
			$qry = $qry->groupOpen('g_search');
			$qry = $qry->and($cpModule['prefix']."_title", 'LIKE', "'%$param_search%'");
			$qry = $qry->or($cpModule['prefix']."_slug", 'LIKE', "'%$param_search%'");
			$qry = $qry->or($cpModule['prefix']."_url", 'LIKE', "'%$param_search%'");
			$qry = $qry->groupClose('g_search');


		}
		$datatable  = ['draw' => $param_draw, 'total' => $total, 'filter' => $qry->select()->num()];

		// check for start and length
		$param_start  = \lib\utility::get('start');
		$param_length = \lib\utility::get('length');
		$param_sortby = \lib\utility::get('sortby');
		$param_order  = \lib\utility::get('order');


		if(!$param_start)
		{
			$param_start = 0;
		}
		if(!$param_length)
		{
			if($total>500)
			{
				$param_length = 10;
			}
			else
			{
				$param_length = $total - $param_start;
			}
		}

		if(!$param_sortby)
		{
			$param_sortby = 'id';
		}
		if(!$param_order)
		{
			$param_order = 'DESC';
		}


		$qry = $qry->limit($param_start, $param_length);
		$tmp_result = $qry->order($param_sortby, $param_order);



		// get only datatable fields on sql for optimizing size of json
		$col = array('id');
		if(!$tmp_columns)
		{
			return;
		}
		foreach ($tmp_columns as $field => $attr)
		{
			if($attr['table'])
			{
				array_push($col, $field);
			}
		}
		$qry = $qry->field(...$col);
		$qry = $qry->select();

		$tmp_result = $qry->allassoc();


		// $tmp_result = $qry->allassoc();

		foreach ($tmp_result as $id => $row)
		{
			foreach ($row as $key => $value)
			{
				if($_rename)
				{
					$prefix = substr($_table, 0, -1).'_';
					// if(substr($key, 0, strlen($prefix) === $prefix))

					if(strpos($key, $prefix) !== false)
					{
						// remove old key
						unset($tmp_result[$id][$key]);
						// transfer value to new key
						$key = substr($key, strlen($prefix));
						$tmp_result[$id][$key] = $value;
					}
				}

				// if field contain json, decode it
				if(substr($value, 0,1) == '{')
					$tmp_result[$id][$key] = json_decode($value, true);

				switch ($key)
				{
					case 'post_status':
					case 'term_status':
						$tmp_result[$id][$key] = T_($value);
						break;

					default:
						# code...
						break;
				}
			}
		}

		$datatable['data']    = $tmp_result;
		$datatable['columns'] = $tmp_columns;

		return $datatable;
	}

	/**
	 * get and set options
	 * @return [type] return json of result
	 */
	public function options()
	{
		$cpModule     = $this->cpModule();
		$param_type   = \lib\utility::get('type');
		$param_search = \lib\utility::get('search');
		$param_id     = \lib\utility::get('id');
		$result       = null;
		switch ($param_type)
		{
			case 'getparentlist':
				$param_parent = \lib\utility::get('parent');
				$qry          = $this->sql()->table($cpModule['table'])
					->where($cpModule['prefix'].'_type', $cpModule['type'])
					->and($cpModule['prefix'].'_parent', $param_parent)
					->field('id', '#`'. $cpModule['prefix'].'_title` as title');
				if($param_search){
					$qry->and($cpModule['prefix'].'_title', 'LIKE', "'%$param_search%'");
				}
				$result = $qry->select()->allassoc();
				break;

			case 'getcatlist':
				$param_parent = \lib\utility::get('parent');
				if(!$param_parent){
					$param_parent = "#NULL";
				}
				$qry          = $this->sql()->table('terms')
					->where('term_type', $cpModule['cat'])
					->and('term_parent', $param_parent)
					->field('id', '#`'. 'term_title` as title');
				$result = $qry->select()->allassoc();
				break;
			case 'getcontent':
				$param_parent = \lib\utility::get('parent');
				$qry          = $this->sql()->table($cpModule['table'])
					->where($cpModule['prefix'].'_type', $cpModule['type'])
					->and('id', $param_id)
					->field('id', '#`'. $cpModule['prefix'].'_content` as content')
					->limit(0,1);
				$result = $qry->select()->assoc();
				$result['content'] = html_entity_decode($result['content']);
			break;
			default:
				break;
		}

		return $result;
	}
}
?>
