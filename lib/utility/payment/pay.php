<?php
namespace dash\utility\payment;


class pay
{

    /**
     * default callback url
     *
     * @var        string
     */
    public static $default_callback_url = 'hook/payment/verify';

    public static $user_id = null;

    public static $log_data = null;


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
            \dash\notif::error(T_("Invalid amount"));
            return false;
        }

        $_bank = mb_strtolower($_bank);

        if(!$_bank)
        {
            \dash\notif::error(T_("Please select a bank port"), 'payment');
            return false;
        }

        // set default timeout for socket
        ini_set("default_socket_timeout", 10);

        if(is_callable(["\\dash\\utility\\payment\\pay\\$_bank", $_bank]))
        {
            \dash\session::set('payment_request_start', true);
            \dash\session::set('payment_verify_amount', null);
            \dash\session::set('payment_verify_status', null);

            return ("\\dash\\utility\\payment\\pay\\$_bank")::$_bank($_user_id, $_amount, $_option);
        }
        else
        {
            \dash\notif::error(T_("This payment is not supported in this system"));
            return false;
        }
    }


    /**
     * Gets the callbck url.
     * for example for parsian payment redirect
     *
     * @param      <type>  $_payment  The payment
     */
    public static function get_callbck_url($_payment)
    {
        $callback_url =  \dash\url::base();

        if($_payment === 'redirect_page')
        {
            $callback_url .= '/hook/autoredirect';
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