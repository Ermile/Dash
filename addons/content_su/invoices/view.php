<?php
namespace content_su\invoices;

class view
{
	public static function config()
	{
		$list = self::invoices_list();
		\dash\data::invoicesList($list);
	}


	public static function invoices_list()
	{
		$meta          = [];
		$meta['admin'] = true;
		$search        = null;
		if(\dash\request::get('search'))
		{
			$search = \dash\request::get('search');
		}

		$result = \dash\db\invoices::search($search, $meta);

		return $result;
	}
}
?>