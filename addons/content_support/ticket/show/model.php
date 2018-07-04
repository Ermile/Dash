<?php
namespace content_support\ticket\show;

class model
{

	/**
	 * UploAads an thumb.
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function upload_file($_name)
	{
		if(\dash\request::files($_name))
		{
			$uploaded_file = \dash\app\file::upload(['debug' => false, 'upload_name' => $_name]);

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


	public static function post()
	{
		$file     = self::upload_file('file');

		// we have an error in upload file1
		if($file === false)
		{
			return false;
		}

		// ready to insert comments
		$args =
		[
			'author'  => \dash\user::detail('displayname'),
			'email'   => \dash\user::detail('email'),
			'type'    => 'ticket',
			'content' => \dash\request::post('desc'),
			'title'   => \dash\request::post('title'),
			'mobile'  => \dash\user::detail("mobile"),
			'user_id' => \dash\user::id(),
			'parent'  => \dash\request::get('id'),
			'file'    => $file,
		];

		// insert comments
		$result = \dash\app\comment::add($args);

		if($result)
		{
			\dash\notif::ok(T_("Your ticket was sended"));
			\dash\redirect::pwd();
		}
	}






	/**
	 * Posts an addmember.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function post22()
	{

		$file1     = self::upload_file('file1');

		// we have an error in upload file1
		if($file1 === false)
		{
			return false;
		}

		$file2     = self::upload_file('file2');

		// we have an error in upload file2
		if($file2 === false)
		{
			return false;
		}

		if($file2 === null && $file1 === null)
		{
			// \dash\notif::warn(T_("To change the image, please re-open the new file"),['element' => ['file2', 'file1']]);
			// return false;
		}
		else
		{
			$request           = [];
			if($file1)
			{
				$request['file1'] = $file1;
			}

			if($file2)
			{
				$request['file2'] = $file2;
			}

			\lib\db\useracademies::update($request, \dash\coding::decode(\dash\request::get('id')));

		}

		$post                 = [];
		$post['nationality']  = \dash\request::post('nationality');
		$post['birthcity']    = \dash\request::post('birthplace');
		$post['issueplace']   = \dash\request::post('issueplace');
		$post['shcode']       = \dash\request::post('shcode');
		$post['pasportcode']  = \dash\request::post('pasportcode');
		$post['pasportdate']  = \dash\request::post('passportdate');
		$post['nationalcode'] = \dash\request::post('nationalcode');

		\lib\app\member::edit($post, \dash\request::get('id'));

		if(\dash\engine\process::status())
		{

			\dash\redirect::pwd();
		}
	}


}
?>