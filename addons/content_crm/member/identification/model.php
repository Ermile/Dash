<?php
namespace content_crm\member\identification;


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




	/**
	 * Posts an addmember.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function post()
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

		$post                 = [];
		if($file1)
		{
			$post['file1'] = $file1;
		}

		if($file2)
		{
			$post['file2'] = $file2;
		}

		$post['nationality']  = \dash\request::post('nationality');
		$post['birthcity']    = \dash\request::post('birthplace');
		$post['issueplace']   = \dash\request::post('issueplace');
		$post['shcode']       = \dash\request::post('shcode');
		$post['pasportcode']  = \dash\request::post('pasportcode');
		$post['pasportdate']  = \dash\request::post('passportdate');
		$post['nationalcode'] = \dash\request::post('nationalcode');
		$post['shfrom']       = \dash\request::post('shfrom');

		\dash\app\user::edit($post, \dash\request::get('id'));

		if(\dash\engine\process::status())
		{

			\dash\redirect::pwd();
		}
	}


}
?>
