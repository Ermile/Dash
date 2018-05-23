<?php
namespace dash\utility;


class convert
{
	private static $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
	private static $ar = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
	private static $fa = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];


	public static function to_en_number($_string)
    {
        $result = str_replace(self::$fa, self::$en, $_string);
        $result = str_replace(self::$ar, self::$en, $result);
        return $result;
    }


    public static function to_fa_number($_string)
    {
        $result = str_replace(self::$en, self::$fa, $_string);
        $result = str_replace(self::$ar, self::$fa, $result);
        return $result;
    }

    public static function ar_to_fa_number($_string)
    {
        $result = str_replace(self::$ar, self::$fa, $_string);
        return $result;
    }


    public static function to_ar_number($_string)
    {
        $result = str_replace(self::$en, self::$ar, $_string);
        $result = str_replace(self::$fa, self::$ar, $result);
        return $result;
    }


    public static function to_barcode($_barcode)
    {
    	$result = self::to_en_number($_barcode);
    	$wrong  = ['چ', 'ژ'];
        $right  = [']', 'C'];
        $result = str_replace($wrong, $right, $result);
        return $result;
    }
}
?>