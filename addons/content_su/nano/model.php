<?php
namespace content_su\nano;

class model
{
	public static function post()
	{

		$fileContent = isset($_POST['fileContent']) ? $_POST['fileContent'] : null;
		if(!$fileContent)
		{
			\dash\notif::error(T_("Please fill the file content"), 'fileContent');
			return false;
		}

		$addr = \content_su\nano\view::get_addr_file(\dash\request::get('file'));
		if(!$addr)
		{
			\dash\notif::error(T_("Invalid file"));
			return false;
		}

		if(\content_su\nano\view::can_edit_file(\dash\request::get('file')))
		{
			if(\dash\file::write($addr, $fileContent))
			{
				\dash\notif::ok(T_("File successfuly update"));
			}
			else
			{
				\dash\notif::error(T_("Can not write file"));
			}
		}
		else
		{
			\dash\notif::error(T_('Can not edit this file!'));
		}
	}


}
?>