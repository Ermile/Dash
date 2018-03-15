<?php
namespace lib\utility\payment\pay;


trait payir
{


    /**
     * pay by payir payment
     * check config of payir
     * save transaction by conditon request
     * redirect to payment url
     * @param      <type>  $_user_id  The user identifier
     * @param      <type>  $_amount   The amount
     * @param      array   $_options  The options
     */
    public static function payir($_user_id, $_amount, $_options = [])
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

        if(!\lib\option::config('payir', 'status'))
        {
            \lib\db\logs::set('pay:payir:status:false', $_user_id, $log_meta);
            \lib\debug::error(T_("The payir payment on this service is locked"));
            return false;
        }

        if(!\lib\option::config('payir', 'api'))
        {
            \lib\db\logs::set('pay:payir:api:not:set', $_user_id, $log_meta);
            \lib\debug::error(T_("The payir payment api not set"));
            return false;
        }

        $payir = [];

        $payir['api']          = \lib\option::config('payir', 'api');
        if(\lib\option::config('payir', 'redirect'))
        {
            $payir['redirect'] = \lib\option::config('payir', 'redirect');
        }
        else
        {
            $payir['redirect'] = self::get_callbck_url('payir');
        }

        // change rial to toman
        // but the plus is toman
        // need less to *10 the plus
        $payir['amount'] = (string) floatval($_amount) * 10;

        $transaction_start =
        [
            'caller'         => 'payment:payir',
            'title'          => T_("Pay by payir payment"),
            'user_id'        => $_user_id,
            'plus'           => $_amount,
            'payment'        => 'payir',
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

        if(!\lib\debug::$status || !$transaction_id)
        {
            return false;
        }


        if(isset($_options['turn_back']))
        {
            // save turn back url to redirect user to this url after coplete pay
            $_SESSION['turn_back'][$transaction_id] = $_options['turn_back'];
        }

        // set in this step and check in other step
        // $payir['specialPaymentId'] = $transaction_id;
        $payir['factorNumber'] = $transaction_id;

        \lib\utility\payment\payment\payir::$user_id  = $_user_id;
        \lib\utility\payment\payment\payir::$log_data = self::$log_data;

        $transId = \lib\utility\payment\payment\payir::pay($payir);

        $temp = \lib\utility\payment\payment\payir::$payment_response;

        if($transId)
        {
            // save amount and autority in session to get when verifying
            $_SESSION['amount']['payir'][$transId]                   = [];
            $_SESSION['amount']['payir'][$transId]['amount']         = floatval($_amount) * 10;
            $_SESSION['amount']['payir'][$transId]['transaction_id'] = $transaction_id;
            $payment_response = json_encode((array) $temp, JSON_UNESCAPED_UNICODE);
            \lib\db\transactions::update(['condition' => 'redirect', 'payment_response' => $payment_response], $transaction_id);
            $redirect_url = "https://pay.ir/payment/gateway/". $transId;
            (new \lib\redirector($redirect_url))->redirect();
            return true;
        }
        else
        {
            return false;
        }
    }
}
?>