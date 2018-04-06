<?php
namespace dash\utility\payment\verify;


trait asanpardakht
{

    /**
     * { function_description }
     *
     * @param      <type>  $_args  The arguments
     */
    public static function asanpardakht($_args)
    {
        self::config();
         $log_meta =
        [
            'data' => self::$log_data,
            'meta' =>
            [
                'input'   => func_get_args(),
                'session' => $_SESSION,
            ]
        ];


        if(!\dash\option::config('asanpardakht', 'status'))
        {
            \dash\db\logs::set('pay:asanpardakht:status:false', $_user_id, $log_meta);
            \dash\notif::error(T_("The asanpardakht payment on this service is locked"));
            return false;
        }

        if(!\dash\option::config('asanpardakht', 'MerchantID'))
        {
            \dash\db\logs::set('pay:asanpardakht:MerchantID:false', $_user_id, $log_meta);
            \dash\notif::error(T_("The asanpardakht payment on this service is locked"));
            return false;
        }

        $username = \dash\option::config('asanpardakht', 'Username');
        $password = \dash\option::config('asanpardakht', 'Password');

        $asanpardakht = [];

        $ReturningParams    = isset($_REQUEST['ReturningParams']) ? (string) $_REQUEST['ReturningParams'] : null;

        \dash\utility\payment\payment\asanpardakht::set_key_iv();
        $ReturningParams    = \dash\utility\payment\payment\asanpardakht::decrypt($ReturningParams);

        $RetArr             = explode(",", $ReturningParams);
        $Amount             = isset($RetArr[0]) ? $RetArr[0] : null;
        $SaleOrderId        = isset($RetArr[1]) ? $RetArr[1] : null;
        $RefId              = isset($RetArr[2]) ? $RetArr[2] : null;
        $ResCode            = isset($RetArr[3]) ? $RetArr[3] : null;
        $ResMessage         = isset($RetArr[4]) ? $RetArr[4] : null;
        $PayGateTranID      = isset($RetArr[5]) ? $RetArr[5] : null;
        $RRN                = isset($RetArr[6]) ? $RetArr[6] : null;
        $LastFourDigitOfPAN = isset($RetArr[7]) ? $RetArr[7] : null;

        if(isset($_SESSION['amount']['asanpardakht'][$RefId]['transaction_id']))
        {
            $transaction_id  = $_SESSION['amount']['asanpardakht'][$RefId]['transaction_id'];
        }
        else
        {
            \dash\db\logs::set('pay:asanpardakht:SESSION:transaction_id:not:found', self::$user_id, $log_meta);
            \dash\notif::error(T_("Your session is lost! We can not find your transaction"));
            return self::turn_back();
        }

        $log_meta['data'] = self::$log_data = $transaction_id;

        $update =
        [
            'amount_end'       => $Amount / 10,
            'condition'        => 'pending',
            'payment_response' => json_encode((array) $_args, JSON_UNESCAPED_UNICODE),
        ];

        \dash\db\transactions::update($update, $transaction_id);
        \dash\db\logs::set('pay:asanpardakht:pending:request', self::$user_id, $log_meta);

        $asanpardakht                 = [];


        if(isset($_SESSION['amount']['asanpardakht'][$RefId]['amount']))
        {
            $Amount_SESSION  = floatval($_SESSION['amount']['asanpardakht'][$RefId]['amount']);
        }
        else
        {
            \dash\db\logs::set('pay:asanpardakht:SESSION:amount:not:found', self::$user_id, $log_meta);
            \dash\notif::error(T_("Your session is lost! We can not find amount"));
            return self::turn_back();
        }

        if($Amount_SESSION != $Amount)
        {
            \dash\db\logs::set('pay:asanpardakht:Amount_SESSION:amount:is:not:equals', self::$user_id, $log_meta);
            \dash\notif::error(T_("Your session is lost! We can not find amount"));
            return self::turn_back();
        }


        if($ResCode == '0' || $ResCode == '00')
        {
            \dash\utility\payment\payment\asanpardakht::$user_id = self::$user_id;
            \dash\utility\payment\payment\asanpardakht::$log_data = self::$log_data;

            $is_ok = \dash\utility\payment\payment\asanpardakht::verify($RetArr);

            $payment_response = \dash\utility\payment\payment\asanpardakht::$payment_response;

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

                \dash\db\transactions::calc_budget($transaction_id, $Amount_SESSION / 10, 0, $update);

                \dash\db\logs::set('pay:asanpardakht:ok:request', self::$user_id, $log_meta);

                \dash\session::set('payment_verify_amount', $Amount_SESSION / 10);

                \dash\session::set('payment_verify_status', 'ok');

                unset($_SESSION['amount']['asanpardakht'][$Token]);

                return self::turn_back($transaction_id);
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
                \dash\db\transactions::update($update, $transaction_id);
                \dash\db\logs::set('pay:asanpardakht:verify_error:request', self::$user_id, $log_meta);
                return self::turn_back($transaction_id);
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
            \dash\db\transactions::update($update, $transaction_id);
            \dash\db\logs::set('pay:asanpardakht:error:request', self::$user_id, $log_meta);
            return self::turn_back($transaction_id);
        }
    }
}
?>