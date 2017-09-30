<?php
namespace lib\utility\payment\pay;
use \lib\debug;
use \lib\option;
use \lib\utility;
use \lib\db\logs;

trait irkish
{


    /**
     * pay by irkish payment
     * check config of irkish
     * save transaction by conditon request
     * redirect to payment url
     * @param      <type>  $_user_id  The user identifier
     * @param      <type>  $_amount   The amount
     * @param      array   $_options  The options
     */
    public static function irkish($_user_id, $_amount, $_options = [])
    {
        $log_meta =
        [
            'data' => self::$log_data,
            'meta' =>
            [
                'input'   => func_get_args(),
                'session' => $_SESSION,
            ]
        ];

        if(!$_user_id || !is_numeric($_user_id))
        {
            return false;
        }

        if(is_numeric($_amount) && $_amount > 0 && $_amount == round($_amount, 0))
        {
            // no problem to continue!
        }
        else
        {
            logs::set('pay:irkish:amount:invalid', $_user_id, $log_meta);
            debug::error(T_("Invalid amount"));
            return false;
        }

        if(!option::config('irkish', 'status'))
        {
            logs::set('pay:irkish:status:false', $_user_id, $log_meta);
            debug::error(T_("The irkish payment on this service is locked"));
            return false;
        }

        if(!option::config('irkish', 'merchantId'))
        {
            logs::set('pay:irkish:merchantId:not:set', $_user_id, $log_meta);
            debug::error(T_("The irkish payment merchantId not set"));
            return false;
        }

        $irkish = [];

        $irkish['paymentId']   = option::config('irkish', 'paymentId');
        $irkish['Sha1']        = option::config('irkish', 'Sha1');
        $irkish['merchantId']  = option::config('irkish', 'merchantId');
        $irkish['description'] = option::config('irkish', 'description');


        if(option::config('irkish', 'revertURL'))
        {
            $irkish['revertURL'] = option::config('irkish', 'revertURL');
        }
        else
        {
            $irkish['revertURL'] = self::get_callbck_url('irkish');
        }

        // change rial to toman
        // but the plus is toman
        // need less to *10 the plus
        $irkish['amount'] = (string) floatval($_amount) * 10;

        $transaction_start =
        [
            'caller'         => 'payment:irkish',
            'title'          => T_("Pay by irkish payment"),
            'user_id'        => $_user_id,
            'plus'           => $_amount,
            'payment'        => 'irkish',
            'type'           => 'money',
            'unit'           => 'toman',
            'date'           => date("Y-m-d H:i:s"),
            'amount_request' => $_amount,
        ];

        //START TRANSACTION BY CONDITION REQUEST
        $transaction_id = \lib\utility\payment\transactions::start($transaction_start);

        $log_meta['data'] = self::$log_data = $transaction_id;

        if(!debug::$status || !$transaction_id)
        {
            return false;
        }

        if(isset($_options['turn_back']))
        {
            // save turn back url to redirect user to this url after coplete pay
            $_SESSION['turn_back'][$transaction_id] = $_options['turn_back'];
        }

        // set in this step and check in other step
        // $irkish['specialPaymentId'] = $transaction_id;
        $irkish['invoiceNo'] = $transaction_id;


        \lib\utility\payment\payment\irkish::$user_id  = $_user_id;
        \lib\utility\payment\payment\irkish::$log_data = self::$log_data;

        $token = \lib\utility\payment\payment\irkish::pay($irkish);

        if($token)
        {
            // save amount and autority in session to get when verifying
            $_SESSION['amount']['irkish'][$token]                   = [];
            $_SESSION['amount']['irkish'][$token]['amount']         = floatval($_amount) * 10;
            $_SESSION['amount']['irkish'][$token]['transaction_id'] = $transaction_id;

            $payment_response = json_encode((array) [], JSON_UNESCAPED_UNICODE);
            \lib\db\transactions::update(['condition' => 'redirect', 'payment_response' => $payment_response], $transaction_id);

            // redirect to enter/redirect
            \lib\session::set('redirect_page_url', 'https://ikc.shaparak.ir/TPayment/Payment/index');
            \lib\session::set('redirect_page_method', 'post');
            \lib\session::set('redirect_page_args', ['token' => $token, 'merchantId' => option::config('irkish', 'merchantId')]);
            \lib\session::set('redirect_page_title', T_("Redirect to iran kish payment"));
            \lib\session::set('redirect_page_button', T_("Redirect"));
            \lib\debug::msg('direct', true);
            (new \lib\redirector(self::get_callbck_url('redirect_page')))->redirect();
            return true;
        }
        else
        {
            return false;
        }
    }
}
?>