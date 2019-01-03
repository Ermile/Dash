<?php
namespace dash\utility\payment\pay;


class zarinpal
{

     /**
     * pay by zarinpal payment
     * check config of zarinpal
     * save transaction by conditon request
     * redirect to payment url
     *
     * @param      <type>  $_user_id  The user identifier
     * @param      <type>  $_amount   The amount
     * @param      array   $_options  The options
     */
    public static function zarinpal($_user_id, $_amount, $_options = [])
    {
        $log_meta =
        [
            'data' => \dash\utility\payment\pay::$log_data,
            'meta' =>
            [
                'input'   => func_get_args(),
            ]
        ];

        if(!\dash\option::config('zarinpal', 'status'))
        {
            \dash\db\logs::set('pay:zarinpal:status:false', $_user_id, $log_meta);
            \dash\notif::error(T_("The zarinpal payment on this service is locked"));
            return false;
        }

        if(!\dash\option::config('zarinpal', 'MerchantID'))
        {
            \dash\db\logs::set('pay:zarinpal:MerchantID:not:set', $_user_id, $log_meta);
            \dash\notif::error(T_("The zarinpal payment MerchantID not set"));
            return false;
        }

        $zarinpal = [];
        $zarinpal['MerchantID'] = \dash\option::config('zarinpal', 'MerchantID');

        if(\dash\option::config('zarinpal', 'Description'))
        {
            $zarinpal['Description'] = \dash\option::config('zarinpal', 'Description');
        }

        if(\dash\option::config('zarinpal', 'CallbackURL'))
        {
            $zarinpal['CallbackURL'] = \dash\option::config('zarinpal', 'CallbackURL');
        }
        else
        {
            $zarinpal['CallbackURL'] = \dash\utility\payment\pay::get_callbck_url('zarinpal');
        }

        $zarinpal['Amount'] = $_amount;

        if(isset($_options['mobile']))
        {
            $zarinpal['Mobile'] = $_options['mobile'];
        }


        if(isset($_options['email']))
        {
            $zarinpal['Email'] = $_options['email'];
        }

        $transaction_start =
        [
            'caller'         => 'payment:zarinpal',
            'title'          => T_("Pay by zarinpal payment"),
            'user_id'        => $_user_id,
            'plus'           => $_amount,
            'payment'        => 'zarinpal',
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
        $transaction_id = \dash\utility\payment\transactions::start($transaction_start);

        $log_meta['data'] = \dash\utility\payment\pay::$log_data = $transaction_id;

        if(!\dash\engine\process::status() || !$transaction_id)
        {
            return false;
        }

        if(isset($_options['turn_back']))
        {
            // save turn back url to redirect user to this url after coplete pay
            $_SESSION['turn_back'][$transaction_id] = $_options['turn_back'];
        }

        \dash\utility\payment\payment\zarinpal::$user_id  = $_user_id;
        \dash\utility\payment\payment\zarinpal::$log_data = \dash\utility\payment\pay::$log_data;

        $redirect = \dash\utility\payment\payment\zarinpal::pay($zarinpal);

        if($redirect)
        {
            $payment_response = \dash\utility\payment\payment\zarinpal::$payment_response;
            if(isset($payment_response->Authority))
            {
                // save amount and autority in session to get when verifying
                $_SESSION['amount']['zarinpal'][$payment_response->Authority]                   = [];
                $_SESSION['amount']['zarinpal'][$payment_response->Authority]['amount']         = $_amount;
                $_SESSION['amount']['zarinpal'][$payment_response->Authority]['transaction_id'] = $transaction_id;

                $payment_response = json_encode((array) $payment_response, JSON_UNESCAPED_UNICODE);
                \dash\utility\payment\transactions::update(['condition' => 'redirect', 'payment_response' => $payment_response], $transaction_id);

                // redirect to bank
                \dash\redirect::to($redirect);

                return true;
            }
            else
            {
                \dash\db\logs::set('pay:zarinpal:Authority:not:set', $_user_id, $log_meta);
                \dash\notif::error(T_("Zarinpal payment Authority not found"));
                return false;
            }
        }
        else
        {
            return false;
        }
    }

}
?>