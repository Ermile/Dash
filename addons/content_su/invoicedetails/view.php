<?php
namespace content_su\invoicedetails;

class view
{
	public static function config()
	{
		$list  = self::invoicedetails_list();
		\dash\data::invoicedetailsList($list);
	}


	public static function invoicedetails_list()
	{
		$meta          = [];
		$meta['admin'] = true;
		$search        = null;
		if(\dash\request::get('search'))
		{
			$search = \dash\request::get('search');
		}

		$result = \dash\db\invoice_details::search($search, $meta);

		return $result;
	}
}
?>