<?php
namespace content_cp\terms;


class model extends \content_cp\main2\model
{
	/**
	 * Uploads a thumb.
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function upload_thumb()
	{
		if(\lib\utility::files('thumb'))
		{
			$uploaded_file = \lib\app\file::upload(['debug' => false, 'upload_name' => 'thumb']);

			if(isset($uploaded_file['url']))
			{
				return $uploaded_file['url'];
			}
			// if in upload have error return
			if(!\lib\debug::$status)
			{
				return false;
			}
		}
		return null;
	}


	public function post_service()
	{

		$post           = [];
		$post['title']  = \lib\utility::post('title');
		$post['count']  = \lib\utility::post('count');
		$post['amount'] = \lib\utility::post('amount');
		$post['desc']   = \lib\utility::post('desc');
		$post['status'] = \lib\utility::post('status') ? 'enable' : 'disable' ;
		$post['type']   = 'expertise';

		$file = self::upload_thumb();
		if($file === false)
		{
			return false;
		}

		if($file)
		{
			$post['fileurl'] = $file;
		}

		if(\lib\utility::get('edit'))
		{
			$id = \lib\utility::get('edit');
			\lib\app\need::edit($id, $post, ['service' => true]);
		}
		else
		{
			\lib\app\need::add($post, ['service' => true]);
		}

		if(\lib\debug::$status)
		{
			if(\lib\utility::get('edit'))
			{
				\lib\debug::true(T_("Way successfully edited"));
				$this->redirector($this->url('baseFull'). '/service/options');
			}
			else
			{
				\lib\debug::true(T_("Way successfully added"));
				$this->redirector($this->url('full'));
			}
		}
	}
}
?>
