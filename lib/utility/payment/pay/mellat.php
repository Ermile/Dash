<?php
namespace dash\utility\payment\pay;


class mellat
{


    /**
     * pay by mellat payment
     * check config of mellat
     * save transaction by conditon request
     * redirect to payment url
     * @param      <type>  $_user_id  The user identifier
     * @param      <type>  $_amount   The amount
     * @param      array   $_options  The options
     */
    public static function mellat($_user_id, $_amount, $_options = [])
    {
        $log_meta =
        [
            'data' => \dash\utility\payment\pay::$log_data,
            'meta' =>
            [
                'input'   => func_get_args(),
                'session' => $_SESSION,
            ]
        ];

        if(!\dash\option::config('mellat', 'status'))
        {
            \dash\db\logs::set('pay:mellat:status:false', $_user_id, $log_meta);
            \dash\notif::error(T_("The mellat payment on this service is locked"));
            return false;
        }

        if(!\dash\option::config('mellat', 'TerminalId'))
        {
            \dash\db\logs::set('pay:mellat:TerminalId:null', $_user_id, $log_meta);
            \dash\notif::error(T_("The mellat payment TerminalId not set"));
            return false;
        }

        if(!\dash\option::config('mellat', 'UserName'))
        {
            \dash\db\logs::set('pay:mellat:UserName:null', $_user_id, $log_meta);
            \dash\notif::error(T_("The mellat payment UserName not set"));
            return false;
        }

        $mellat                   = [];
        $mellat['terminalId']     = \dash\option::config('mellat', 'TerminalId');
        $mellat['userName']       = \dash\option::config('mellat', 'UserName');
        $mellat['userPassword']   = \dash\option::config('mellat', 'UserPassword');
        $mellat['localDate']      = date("Ymd");
        $mellat['localTime']      = date("His");
        $mellat['additionalData'] = null;
        $mellat['payerId']        = \dash\user::id();


        if(\dash\option::config('mellat', 'callBackUrl'))
        {
            $mellat['callBackUrl'] = \dash\option::config('mellat', 'callBackUrl');
        }
        else
        {
            $mellat['callBackUrl'] = \dash\utility\payment\pay::get_callbck_url('mellat');
        }

        // change rial to toman
        // but the plus is toman
        // need less to *10 the plus
        $mellat['amount'] = (string) floatval($_amount) * 10;

        $transaction_start =
        [
            'caller'         => 'payment:mellat',
            'title'          => T_("Pay by mellat payment"),
            'user_id'        => $_user_id,
            'plus'           => $_amount,
            'payment'        => 'mellat',
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

        // set in this step and check in other step
        // $mellat['specialPaymentId'] = $transaction_id;
        $mellat['orderId'] = $transaction_id;


        \dash\utility\payment\payment\mellat::$user_id  = $_user_id;
        \dash\utility\payment\payment\mellat::$log_data = \dash\utility\payment\pay::$log_data;

        $RefId = \dash\utility\payment\payment\mellat::pay($mellat);

        if($RefId)
        {
            // save amount and autority in session to get when verifying
            $_SESSION['amount']['mellat'][$RefId]                   = [];
            $_SESSION['amount']['mellat'][$RefId]['amount']         = floatval($_amount) * 10;
            $_SESSION['amount']['mellat'][$RefId]['transaction_id'] = $transaction_id;

            $payment_response = json_encode((array) [], JSON_UNESCAPED_UNICODE);
            \dash\utility\payment\transactions::update(['condition' => 'redirect', 'payment_response' => $payment_response], $transaction_id);

            // redirect to enter/redirect
            \dash\session::set('redirect_page_url', 'https://bpm.shaparak.ir/pgwchannel/startpay.mellat');
            \dash\session::set('redirect_page_method', 'post');
            \dash\session::set('redirect_page_args', ['RefId' => $RefId]);
            \dash\session::set('redirect_page_title', T_("Redirect to mellat payment"));
            \dash\session::set('redirect_page_button', T_("Redirect"));
            \dash\notif::direct();
            \dash\redirect::to(\dash\utility\payment\pay::get_callbck_url('redirect_page'));
            return true;
        }
        else
        {
            return false;
        }
    }
}
?>