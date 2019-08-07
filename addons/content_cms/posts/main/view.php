<?php
namespace content_cms\posts\main;


class view
{
	public static function myDataType()
	{
		if(\dash\data::dataRow_type())
		{
			\dash\data::myDataType(\dash\data::dataRow_type());
		}
		elseif(\dash\request::get('type'))
		{
			\dash\data::myDataType(\dash\request::get('type'));
		}
		else
		{
			\dash\data::myDataType('post');
		}

		// check subdomain
	}
}
?>