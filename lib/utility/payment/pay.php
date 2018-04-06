<?php
namespace dash\utility\payment;


class pay
{

    /**
     * default callback url
     *
     * @var        string
     */
    public static $default_callback_url = 'enter/payment/verify';

    public static $user_id = null;

    public static $log_data = null;

    use pay\zarinpal;
    use pay\parsian;
    use pay\irkish;
    use pay\payir;
    use pay\asanpardakht;

    /**
    * start pay
    */
    public static function start($_user_id, $_bank, $_amount, $_option = [])
    {
        if(!$_user_id || !is_numeric($_user_id))
        {
            if($_user_id === 'unverify')
            {
                // pay as undefined user in some project
            }
            else
            {
                return false;
            }
        }


        if(is_numeric($_amount) && $_amount > 0 && $_amount == round($_amount, 0))
        {
            // no problem to continue!
        }
        else
        {
            \lib\db\logs::set('pay:irkish:amount:invalid', $_user_id);
            \lib\notif::error(T_("Invalid amount"));
            return false;
        }

        $_bank = mb_strtolower($_bank);

        if(method_exists("\\lib\\utility\\payment\\pay", $_bank))
        {

            \lib\session::set('payment_request_start', true);
            \lib\session::set('payment_verify_amount', null);
            \lib\session::set('payment_verify_status', null);


            return \lib\utility\payment\pay::$_bank($_user_id, $_amount, $_option);
        }
        else
        {
            \lib\notif::error(T_("This payment is not supported in this system"));
            return false;
        }
    }


    /**
     * Gets the callbck url.
     * for example for parsian payment redirect
     * http://tejarak.com/fa/enter/payment/verify/parsian
     *
     * @param      <type>  $_payment  The payment
     */
    private static function get_callbck_url($_payment)
    {
        $host = \lib\url::site();
        $callback_url =  $host;
        $callback_url .= $lang;

        if($_payment === 'redirect_page')
        {
            $callback_url .= '/enter/autoredirect';
        }
        else
        {
            $callback_url .= '/'. self::$default_callback_url;
            $callback_url .= '/'. $_payment;
        }
        return $callback_url;
    }
}
?>