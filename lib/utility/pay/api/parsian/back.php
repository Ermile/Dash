<?php
namespace dash\utility\payment\verify;


class parsian
{

    /**
     * { function_description }
     *
     * @param      <type>  $_args  The arguments
     */
    public static function parsian($_args)
    {
        \dash\utility\payment\verify::config();

        $log_meta =
        [
            'data' => \dash\utility\payment\verify::$log_data,
            'meta' =>
            [
                'input'   => func_get_args(),
            ]
        ];

        if(!\dash\option::config('parsian', 'status'))
        {
            \dash\db\logs::set('pay:parsian:status:false', \dash\utility\payment\verify::$user_id, $log_meta);
            \dash\notif::error(T_("The parsian payment on this service is locked"));
            return \dash\utility\payment\verify::turn_back();
        }

        if(!\dash\option::config('parsian', 'LoginAccount'))
        {
            \dash\db\logs::set('pay:parsian:LoginAccount:not:set', \dash\utility\payment\verify::$user_id, $log_meta);
            \dash\notif::error(T_("The parsian payment LoginAccount not set"));
            return \dash\utility\payment\verify::turn_back();
        }

        $Token          = isset($_REQUEST['Token'])           ? (string) $_REQUEST['Token']          : null;
        $OrderId        = isset($_REQUEST['OrderId'])         ? (string) $_REQUEST['OrderId']        : null;
        $status         = isset($_REQUEST['status'])          ? (string) $_REQUEST['status']         : null;
        $TerminalNo     = isset($_REQUEST['TerminalNo'])      ? (string) $_REQUEST['TerminalNo']     : null;
        $RRN            = isset($_REQUEST['RRN'])             ? (string) $_REQUEST['RRN']            : null;
        $TspToken       = isset($_REQUEST['TspToken'])        ? (string) $_REQUEST['TspToken']       : null;
        $HashCardNumber = isset($_REQUEST['HashCardNumber'])  ? (string) $_REQUEST['HashCardNumber'] : null;
        $Amount         = isset($_REQUEST['Amount'])          ? (string) $_REQUEST['Amount']         : null;
        $Amount         = str_replace(',', '', $Amount);
        if(!$Token)
        {
            \dash\db\logs::set('pay:parsian:Token:verify:not:found', \dash\utility\payment\verify::$user_id, $log_meta);
            \dash\notif::error(T_("The parsian payment Token not set"));
            return \dash\utility\payment\verify::turn_back();
        }

        if(isset($_SESSION['amount']['parsian'][$Token]['transaction_id']))
        {
            $transaction_id  = $_SESSION['amount']['parsian'][$Token]['transaction_id'];
        }
        else
        {
            \dash\db\logs::set('pay:parsian:SESSION:transaction_id:not:found', \dash\utility\payment\verify::$user_id, $log_meta);
            \dash\notif::error(T_("Your session is lost! We can not find your transaction"));
            return \dash\utility\payment\verify::turn_back();
        }

        $log_meta['data'] = \dash\utility\payment\verify::$log_data = $transaction_id;

        $update =
        [
            'amount_end'       => $Amount / 10,
            'condition'        => 'pending',
            'payment_response' => json_encode((array) $_args, JSON_UNESCAPED_UNICODE),
        ];

        \dash\utility\payment\transactions::update($update, $transaction_id);
        \dash\db\logs::set('pay:parsian:pending:request', \dash\utility\payment\verify::$user_id, $log_meta);

        $parsian                 = [];
        $parsian['LoginAccount'] = \dash\option::config('parsian', 'LoginAccount');
        $parsian['Token']        = $Token;

        if(isset($_SESSION['amount']['parsian'][$Token]['amount']))
        {
            $Amount_SESSION  = floatval($_SESSION['amount']['parsian'][$Token]['amount']);
        }
        else
        {
            \dash\db\logs::set('pay:parsian:SESSION:amount:not:found', \dash\utility\payment\verify::$user_id, $log_meta);
            \dash\notif::error(T_("Your session is lost! We can not find amount"));
            return \dash\utility\payment\verify::turn_back();
        }

        if($Amount_SESSION != $Amount)
        {
            \dash\db\logs::set('pay:parsian:Amount_SESSION:amount:is:not:equals', \dash\utility\payment\verify::$user_id, $log_meta);
            \dash\notif::error(T_("Your session is lost! We can not find amount"));
            return \dash\utility\payment\verify::turn_back();
        }


        if($status === '0' && intval($Token) > 0)
        {
            \dash\utility\payment\payment\parsian::$user_id = \dash\utility\payment\verify::$user_id;
            \dash\utility\payment\payment\parsian::$log_data = \dash\utility\payment\verify::$log_data;

            $is_ok = \dash\utility\payment\payment\parsian::verify($parsian);

            $payment_response = \dash\utility\payment\payment\parsian::$payment_response;

            $log_meta['meta']['payment_response'] = (array) $payment_response;

            $payment_response = json_encode((array) $payment_response, JSON_UNESCAPED_UNICODE);

            if($is_ok)
            {
                $update =
                [
                    'amount_end'       => $Amount_SESSION / 10,
                    'condition'        => 'ok',
                    'verify'           => 1,
                    'payment_response' => $payment_response,
                ];


                \dash\utility\payment\verify::$final_verify         = true;
                \dash\utility\payment\verify::$final_transaction_id = $transaction_id;


                \dash\utility\payment\transactions::calc_budget($transaction_id, $Amount_SESSION / 10, 0, $update);

                \dash\db\logs::set('pay:parsian:ok:request', \dash\utility\payment\verify::$user_id, $log_meta);

                \dash\session::set('payment_verify_amount', $Amount_SESSION / 10);

                \dash\session::set('payment_verify_status', 'ok');

                unset($_SESSION['amount']['parsian'][$Token]);

                return \dash\utility\payment\verify::turn_back($transaction_id);
            }
            else
            {
                $update =
                [
                    'amount_end'       => $Amount_SESSION / 10,
                    'condition'        => 'verify_error',
                    'payment_response' => $payment_response,
                ];
                \dash\session::set('payment_verify_status', 'verify_error');
                \dash\utility\payment\transactions::update($update, $transaction_id);
                \dash\db\logs::set('pay:parsian:verify_error:request', \dash\utility\payment\verify::$user_id, $log_meta);
                return \dash\utility\payment\verify::turn_back($transaction_id);
            }
        }
        else
        {
            $update =
            [
                'amount_end'       => $Amount_SESSION / 10,
                'condition'        => 'error',
                'payment_response' => json_encode((array) $_args, JSON_UNESCAPED_UNICODE),
            ];
            \dash\session::set('payment_verify_status', 'error');
            \dash\utility\payment\transactions::update($update, $transaction_id);
            \dash\db\logs::set('pay:parsian:error:request', \dash\utility\payment\verify::$user_id, $log_meta);
            return \dash\utility\payment\verify::turn_back($transaction_id);
        }
    }
}
?>