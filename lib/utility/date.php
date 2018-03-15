<?php
namespace lib\utility;

class date
{
	/**
     * get month precent
     *
     *
     * @param      <type>  $_type  The type
     */
    public static function month_precent($_type = null)
    {
    	$lang = \lib\language::get_language();

    	if($lang === 'fa')
    	{
			$d = intval(\lib\utility\jdate::date("d", false, false));
			$m = intval(\lib\utility\jdate::date("m", false, false));
			$t = intval(\lib\utility\jdate::date("t", false, false));
    	}
    	else
    	{
			$d = intval(date("d"));
			$m = intval(date("m"));
			$t = intval(date("t"));
    	}

        $left   = round(($d * 100) / $t);
        $remain = round((($t - $d) * 100) / $t);

        $return = null;
        switch ($_type)
        {
            case 'left':
                $return = $left;
                break;
            case 'remain':
                $return = $remain;
                break;
            default:
                $return =
                [
					'left'   => $left,
					'remain' => $remain,
					'count'  => $t,
                ];
                break;
        }
        return $return;
    }
}
?>
