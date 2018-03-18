<?php
namespace lib\utility\payment\pay;


trait asanpardakht
{


    /**
     * pay by asanpardakht payment
     * check config of asanpardakht
     * save transaction by conditon request
     * redirect to payment url
     * @param      <type>  $_user_id  The user identifier
     * @param      <type>  $_amount   The amount
     * @param      array   $_options  The options
     */
    public static function asanpardakht($_user_id, $_amount, $_options = [])
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


        if(!\lib\option::config('asanpardakht', 'status'))
        {
            \lib\db\logs::set('pay:asanpardakht:status:false', $_user_id, $log_meta);
            \lib\notif::error(T_("The asanpardakht payment on this service is locked"));
            return false;
        }

        if(!\lib\option::config('asanpardakht', 'MerchantID'))
        {
            \lib\db\logs::set('pay:asanpardakht:MerchantID:false', $_user_id, $log_meta);
            \lib\notif::error(T_("The asanpardakht payment on this service is locked"));
            return false;
        }

        $username = \lib\option::config('asanpardakht', 'Username');
        $password = \lib\option::config('asanpardakht', 'Password');

        $asanpardakht = [];

        if(\lib\option::config('asanpardakht', 'CallBackUrl'))
        {
            $asanpardakht['CallBackUrl'] = \lib\option::config('asanpardakht', 'CallBackUrl');
        }
        else
        {
            $asanpardakht['CallBackUrl'] = self::get_callbck_url('asanpardakht');
        }

        // change rial to toman
        // but the plus is toman
        // need less to *10 the plus
        $price = floatval($_amount) * 10;

        $transaction_start =
        [
            'caller'         => 'payment:asanpardakht',
            'title'          => T_("Pay by asanpardakht payment"),
            'user_id'        => $_user_id,
            'plus'           => $_amount,
            'payment'        => 'asanpardakht',
            'type'           => 'money',
            'unit'           => 'toman',
            'date'           => date("Y-m-d H:i:s"),
            'amount_request' => $_amount,
        ];

        if(isset($_options['other_field']) && is_array($_options['other_field']))
        {
            $transaction_start['other_field'] = $_options['other_field'];
        }

        //START TRANSACTION BY CONDITION REQUEST
        $transaction_id = \lib\utility\payment\transactions::start($transaction_start);

        $log_meta['data'] = self::$log_data = $transaction_id;

        if(!\lib\engine\process::status() || !$transaction_id)
        {
            return false;
        }

        $orderId        = $transaction_id;
        $localDate      = date("Ymd His");
        $additionalData = "";
        $callBackUrl    = $asanpardakht['CallBackUrl'];
        $req            = "1,{$username},{$password},{$orderId},{$price},{$localDate},{$additionalData},{$callBackUrl},0";

        $asanpardakht_args =
        [
            'orderId'        => $orderId,
            'localDate'      => $localDate,
            'additionalData' => $additionalData,
            'callBackUrl'    => $callBackUrl,
            'req'            => $req,
        ];

        if(isset($_options['turn_back']))
        {
            // save turn back url to redirect user to this url after coplete pay
            $_SESSION['turn_back'][$transaction_id] = $_options['turn_back'];
        }

        \lib\utility\payment\payment\asanpardakht::$user_id = $_user_id;
        \lib\utility\payment\payment\asanpardakht::$log_data = self::$log_data;

        $RefId = \lib\utility\payment\payment\asanpardakht::pay($asanpardakht_args);

        $payment_response = \lib\utility\payment\payment\asanpardakht::$payment_response;

        if($RefId)
        {
            // save amount and autority in session to get when verifying
            $_SESSION['amount']['asanpardakht'][$RefId]                   = [];
            $_SESSION['amount']['asanpardakht'][$RefId]['amount']         = floatval($_amount) * 10;
            $_SESSION['amount']['asanpardakht'][$RefId]['transaction_id'] = $transaction_id;

            $payment_response = json_encode((array) $payment_response, JSON_UNESCAPED_UNICODE);

            \lib\db\transactions::update(['condition' => 'redirect', 'payment_response' => $payment_response], $transaction_id);

            // redirect to enter/redirect
            \lib\session::set('redirect_page_url', 'https://asan.shaparak.ir/');
            \lib\session::set('redirect_page_method', 'post');
            \lib\session::set('redirect_page_args', ['RefId' => $RefId]);
            \lib\session::set('redirect_page_title', T_("Redirect to asanpardakht payment"));
            \lib\session::set('redirect_page_button', T_("Redirect"));
            \lib\notif::direct();
            \lib\redirect::to(self::get_callbck_url('redirect_page'));
            return true;

        }
        else
        {
            return false;
        }
    }

}
?>