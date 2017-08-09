<?php
namespace lib\utility\payment;
use \lib\debug;

class parsian
{

	/**
     * auto save logs
     *
     * @var        boolean
     */
    public static $save_log = false;
    // to save log for this user
    public static $user_id  = null;

    use parsian\pay;
    use parsian\verify;
    use parsian\message;

}
?>